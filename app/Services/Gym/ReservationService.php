<?php

namespace App\Services\Gym;

use App\Models\Reservation;
use App\Models\FieldTimeSlot;
use Carbon\Carbon;

class ReservationService
{
    /**
     * Check if a time slot is available on a given date.
     */
    public function isAvailable(int $slotId, string $date): bool
    {
        return !Reservation::where('field_time_slot_id', $slotId)
            ->where('reservation_date', $date)
            ->whereIn('status', ['confirmed', 'pending'])
            ->exists();
    }

    /**
     * Create a single (occasional) reservation.
     *
     * @throws \RuntimeException if the slot is already booked.
     */
    public function create(array $data): Reservation
    {
        if (!$this->isAvailable($data['field_time_slot_id'], $data['reservation_date'])) {
            throw new \RuntimeException('El turno ya está reservado para esa fecha.');
        }

        return Reservation::create($data + ['type' => 'occasional']);
    }

    /**
     * Create a recurring reservation for every occurrence of a weekday
     * between $startsAt and $endsAt.
     *
     * @return Reservation[]
     * @throws \RuntimeException if any occurrence has a conflict.
     */
    public function createRecurring(array $data): array
    {
        $start = Carbon::parse($data['reservation_date']);
        $end   = Carbon::parse($data['recurring_until'] ?? $start->copy()->addMonths(1));

        $dayMap = [
            'monday' => Carbon::MONDAY, 'tuesday' => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY, 'thursday' => Carbon::THURSDAY,
            'friday' => Carbon::FRIDAY, 'saturday' => Carbon::SATURDAY,
            'sunday' => Carbon::SUNDAY,
        ];

        $targetDay = $dayMap[$data['recurrence_day']] ?? $start->dayOfWeek;
        $current   = $start->copy()->nextOrCurrent($targetDay);

        $reservations = [];
        while ($current->lte($end)) {
            $date = $current->toDateString();
            if (!$this->isAvailable($data['field_time_slot_id'], $date)) {
                throw new \RuntimeException("Conflicto de reserva en la fecha {$date}.");
            }
            $reservations[] = Reservation::create(array_merge($data, [
                'reservation_date' => $date,
                'type'             => 'recurring',
            ]));
            $current->addWeek();
        }

        return $reservations;
    }

    /**
     * Get all reservations for a given week starting from $weekStart.
     */
    public function getWeeklyCalendar(Carbon $weekStart): array
    {
        $weekEnd = $weekStart->copy()->endOfWeek();
        $reservations = Reservation::with('timeSlot')
            ->whereBetween('reservation_date', [$weekStart, $weekEnd])
            ->where('status', '!=', 'cancelled')
            ->get();

        $calendar = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i)->toDateString();
            $calendar[$day] = $reservations->where('reservation_date', $day)->values();
        }

        return $calendar;
    }
}
