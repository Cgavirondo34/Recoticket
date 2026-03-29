<?php

namespace Tests\Feature\Field;

use App\Models\FieldReservation;
use App\Models\FieldSlot;
use App\Services\Field\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReservationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReservationService();
    }

    private function makeSlot(array $overrides = []): FieldSlot
    {
        return FieldSlot::create(array_merge([
            'name'        => 'Cancha 1 — 18:00',
            'start_time'  => '18:00:00',
            'end_time'    => '19:00:00',
            'days_of_week'=> [1, 2, 3, 4, 5, 6, 7], // all days
            'price'       => 5000,
            'active'      => true,
            'max_bookings'=> 1,
        ], $overrides));
    }

    public function test_create_reservation_succeeds(): void
    {
        $slot = $this->makeSlot();

        $reservation = $this->service->create($slot, '2025-07-14', [
            'customer_name' => 'Equipo A',
            'customer_phone'=> '1112345678',
        ]);

        $this->assertInstanceOf(FieldReservation::class, $reservation);
        $this->assertEquals('confirmed', $reservation->status);
        $this->assertEquals('2025-07-14', $reservation->reservation_date->toDateString());
        $this->assertEquals(5000, $reservation->price);
    }

    public function test_double_booking_throws_exception(): void
    {
        $slot = $this->makeSlot();
        $date = '2025-07-14';

        // First booking succeeds
        $this->service->create($slot, $date, ['customer_name' => 'Equipo A']);

        // Second booking on same slot+date should throw
        $this->expectException(\RuntimeException::class);
        $this->service->create($slot, $date, ['customer_name' => 'Equipo B']);
    }

    public function test_cancelled_reservation_allows_rebooking(): void
    {
        $slot = $this->makeSlot();
        $date = '2025-07-14';

        // Create and cancel first booking
        $first = $this->service->create($slot, $date, ['customer_name' => 'Equipo A']);
        $first->update(['status' => 'cancelled']);
        $first->delete(); // soft delete

        // Now a new booking on same slot+date should succeed
        $second = $this->service->create($slot, $date, ['customer_name' => 'Equipo B']);
        $this->assertEquals('confirmed', $second->status);
    }

    public function test_create_series_generates_reservations(): void
    {
        $slot = $this->makeSlot();

        // Create a weekly Mon/Wed/Fri series for 2 weeks
        $series = $this->service->createSeries($slot, [
            'start_date'       => '2025-07-07', // Mon
            'end_date'         => '2025-07-18', // Fri
            'days_of_week'     => [1, 3, 5], // Mon, Wed, Fri
            'price_per_session'=> 4000,
            'customer_name'    => 'Equipo Fijo',
        ]);

        // 2025-07-07 (Mon), 2025-07-09 (Wed), 2025-07-11 (Fri)
        // 2025-07-14 (Mon), 2025-07-16 (Wed), 2025-07-18 (Fri) = 6 reservations
        $this->assertCount(6, $series->reservations);
    }

    public function test_get_calendar_returns_grouped_reservations(): void
    {
        $slot = $this->makeSlot();

        $this->service->create($slot, '2025-07-01', ['customer_name' => 'A']);
        $this->service->create($slot, '2025-07-03', ['customer_name' => 'B']);

        // Use a second slot so we can add two reservations on same date
        $slot2 = $this->makeSlot(['name' => 'Cancha 2', 'start_time' => '19:00:00', 'end_time' => '20:00:00']);
        $this->service->create($slot2, '2025-07-01', ['customer_name' => 'C']);

        $calendar = $this->service->getCalendar(null, '2025-07-01', '2025-07-05');

        // 2025-07-01 should have 2 reservations, 2025-07-03 should have 1
        $this->assertCount(2, $calendar->get('2025-07-01'));
        $this->assertCount(1, $calendar->get('2025-07-03'));
    }
}
