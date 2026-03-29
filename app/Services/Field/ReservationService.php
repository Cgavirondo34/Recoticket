<?php

namespace App\Services\Field;

use App\Models\FieldReservation;
use App\Models\FieldReservationSeries;
use App\Models\FieldSlot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    /**
     * Create a one-time reservation for a slot on a given date.
     * Prevents double booking.
     *
     * @throws \RuntimeException on double booking
     */
    public function create(FieldSlot $slot, string $date, array $data = []): FieldReservation
    {
        // Check before transaction to avoid nested-savepoint visibility issues
        $this->assertNotDoubleBooked($slot, $date);

        return DB::transaction(function () use ($slot, $date, $data) {

            return FieldReservation::create([
                'tenant_id' => $slot->tenant_id,
                'field_slot_id' => $slot->id,
                'member_id' => $data['member_id'] ?? null,
                'reservation_series_id' => $data['reservation_series_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_whatsapp' => $data['customer_whatsapp'] ?? null,
                'reservation_date' => $date,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'price' => $data['price'] ?? $slot->price,
                'payment_status' => 'pending',
                'status' => 'confirmed',
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'created_by' => $data['created_by'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    /**
     * Create a recurring series and generate individual reservations.
     */
    public function createSeries(FieldSlot $slot, array $data): FieldReservationSeries
    {
        return DB::transaction(function () use ($slot, $data) {
            $series = FieldReservationSeries::create([
                'tenant_id' => $slot->tenant_id,
                'field_slot_id' => $slot->id,
                'member_id' => $data['member_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'] ?? null,
                'days_of_week' => $data['days_of_week'],
                'price_per_session' => $data['price_per_session'] ?? $slot->price,
                'status' => 'active',
                'created_by' => $data['created_by'] ?? null,
            ]);

            $this->generateReservationsForSeries($series);

            return $series;
        });
    }

    /**
     * Generate individual reservations from a series (up to 90 days ahead).
     */
    public function generateReservationsForSeries(FieldReservationSeries $series, int $daysAhead = 90): void
    {
        $slot = $series->slot;
        $start = Carbon::parse($series->start_date);
        $end = $series->end_date
            ? Carbon::parse($series->end_date)
            : $start->copy()->addDays($daysAhead);

        $period = CarbonPeriod::create($start, $end);

        // Pre-fetch all existing reservations for this slot in the date range to avoid N+1 queries
        $existingDates = FieldReservation::where('field_slot_id', $slot->id)
            ->whereDate('reservation_date', '>=', $start->toDateString())
            ->whereDate('reservation_date', '<=', $end->toDateString())
            ->pluck('reservation_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
            ->flip()
            ->all();

        foreach ($period as $date) {
            $dayOfWeek = (int) $date->format('N'); // 1=Mon, 7=Sun
            if (! in_array($dayOfWeek, $series->days_of_week ?? [])) {
                continue;
            }

            // Skip dates that are already booked (use pre-fetched set)
            if (isset($existingDates[$date->toDateString()])) {
                continue;
            }

            FieldReservation::create([
                'tenant_id' => $series->tenant_id,
                'field_slot_id' => $slot->id,
                'member_id' => $series->member_id,
                'reservation_series_id' => $series->id,
                'customer_name' => $series->customer_name,
                'customer_phone' => $series->customer_phone,
                'reservation_date' => $date->toDateString(),
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'price' => $series->price_per_session,
                'payment_status' => 'pending',
                'status' => 'confirmed',
                'created_by' => $series->created_by,
            ]);
        }
    }

    /**
     * Assert a slot is not already booked on a date.
     *
     * @throws \RuntimeException
     */
    private function assertNotDoubleBooked(FieldSlot $slot, string $date): void
    {
        $exists = FieldReservation::where('field_slot_id', $slot->id)
            ->whereDate('reservation_date', $date)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($exists) {
            throw new \RuntimeException("The slot '{$slot->name}' is already booked on {$date}.");
        }
    }

    /**
     * Get reservations for a calendar range, grouped by date.
     */
    public function getCalendar(?int $tenantId, string $from, string $to): \Illuminate\Support\Collection
    {
        return FieldReservation::with(['slot', 'member', 'paymentMethod'])
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->whereDate('reservation_date', '>=', $from)
            ->whereDate('reservation_date', '<=', $to)
            ->where('status', '!=', 'cancelled')
            ->orderBy('reservation_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn($r) => $r->reservation_date->toDateString());
    }
}
