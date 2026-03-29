<?php

namespace Tests\Feature\Finance;

use App\Models\BusinessPartner;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FieldReservation;
use App\Models\FieldSlot;
use App\Models\GymPayment;
use App\Models\Member;
use App\Models\User;
use App\Services\Finance\SettlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettlementServiceTest extends TestCase
{
    use RefreshDatabase;

    private SettlementService $service;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SettlementService();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_calculate_settlement_computes_correct_totals(): void
    {
        $member = Member::create(['full_name' => 'Juan', 'status' => 'active']);
        $slot = FieldSlot::create([
            'name' => 'Cancha 1', 'start_time' => '18:00', 'end_time' => '19:00',
            'days_of_week' => [1,2,3,4,5,6,7], 'price' => 5000, 'active' => true, 'max_bookings' => 1,
        ]);

        // Gym income: 2 confirmed payments in July 2025
        GymPayment::create(['member_id' => $member->id, 'amount' => 15000, 'status' => 'confirmed', 'type' => 'membership', 'paid_at' => '2025-07-10']);
        GymPayment::create(['member_id' => $member->id, 'amount' => 15000, 'status' => 'confirmed', 'type' => 'membership', 'paid_at' => '2025-07-20']);

        // Field income: 1 paid reservation
        FieldReservation::create([
            'field_slot_id' => $slot->id, 'reservation_date' => '2025-07-15',
            'start_time' => '18:00', 'end_time' => '19:00', 'price' => 5000,
            'payment_status' => 'paid', 'status' => 'confirmed',
        ]);

        // Expenses
        $cat = ExpenseCategory::create(['name' => 'Alquiler', 'slug' => 'rent', 'business_unit' => 'shared', 'active' => true]);
        Expense::create(['expense_category_id' => $cat->id, 'description' => 'Alquiler julio', 'amount' => 10000, 'business_unit' => 'shared', 'expense_date' => '2025-07-01']);

        // Partners — 50/50
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        BusinessPartner::create(['user_id' => $user1->id, 'display_name' => 'Socio A', 'gym_percentage' => 50, 'field_percentage' => 50, 'active' => true]);
        BusinessPartner::create(['user_id' => $user2->id, 'display_name' => 'Socio B', 'gym_percentage' => 50, 'field_percentage' => 50, 'active' => true]);

        $settlement = $this->service->calculate(null, 2025, 7);

        $this->assertEquals(30000, $settlement->gym_income);
        $this->assertEquals(5000, $settlement->field_income);
        $this->assertEquals(35000, $settlement->total_income);
        $this->assertEquals(0, $settlement->gym_expenses);
        $this->assertEquals(0, $settlement->field_expenses);
        $this->assertEquals(10000, $settlement->shared_expenses);
        $this->assertEquals(10000, $settlement->total_expenses);

        // gymNet = 30000 - 0 - 5000 = 25000
        // fieldNet = 5000 - 0 - 5000 = 0
        $this->assertEquals(25000, $settlement->gym_net);
        $this->assertEquals(0, $settlement->field_net);
        $this->assertEquals(25000, $settlement->total_net);
        $this->assertEquals('draft', $settlement->status);

        // Each partner gets 50% of each net
        $earnings = $settlement->partner_earnings;
        $this->assertCount(2, $earnings);
        $firstPartner = reset($earnings);
        $this->assertEquals(12500, $firstPartner['gym_share']); // 50% of 25000
        $this->assertEquals(0, $firstPartner['field_share']);
    }

    public function test_cannot_recalculate_closed_settlement(): void
    {
        $settlement = $this->service->calculate(null, 2025, 8);
        $this->service->close($settlement, $this->admin->id);

        $this->expectException(\RuntimeException::class);
        $this->service->calculate(null, 2025, 8);
    }

    public function test_close_settlement_marks_as_closed(): void
    {
        $settlement = $this->service->calculate(null, 2025, 9);
        $closed = $this->service->close($settlement, $this->admin->id, 'Aprobado');

        $this->assertEquals('closed', $closed->status);
        $this->assertEquals($this->admin->id, $closed->closed_by);
        $this->assertNotNull($closed->closed_at);
        $this->assertEquals('Aprobado', $closed->notes);
    }

    public function test_recalculate_draft_updates_values(): void
    {
        $member = Member::create(['full_name' => 'Test', 'status' => 'active']);
        GymPayment::create(['member_id' => $member->id, 'amount' => 5000, 'status' => 'confirmed', 'type' => 'membership', 'paid_at' => '2025-10-15']);

        $first = $this->service->calculate(null, 2025, 10);
        $this->assertEquals(5000, $first->gym_income);

        // Add another payment and recalculate
        GymPayment::create(['member_id' => $member->id, 'amount' => 10000, 'status' => 'confirmed', 'type' => 'membership', 'paid_at' => '2025-10-20']);

        $second = $this->service->calculate(null, 2025, 10);
        $this->assertEquals(15000, $second->gym_income);
        $this->assertEquals($first->id, $second->id); // same record updated
    }
}
