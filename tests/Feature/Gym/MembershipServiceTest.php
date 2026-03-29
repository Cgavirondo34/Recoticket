<?php

namespace Tests\Feature\Gym;

use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\MembershipPlan;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Services\Gym\MembershipService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipServiceTest extends TestCase
{
    use RefreshDatabase;

    private MembershipService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MembershipService();
        $this->user = User::factory()->create(['role' => 'admin']);
    }

    public function test_assign_plan_creates_membership_and_payment(): void
    {
        $member = Member::create([
            'full_name' => 'Juan Pérez',
            'status'    => 'prospect',
        ]);

        $plan = MembershipPlan::create([
            'name'          => 'Mensual',
            'price'         => 15000,
            'duration_days' => 30,
            'active'        => true,
        ]);

        $membership = $this->service->assignPlan($member, $plan, [
            'start_date' => '2025-01-01',
            'created_by' => $this->user->id,
        ]);

        $this->assertEquals('active', $membership->status);
        $this->assertEquals('2025-01-01', $membership->start_date->toDateString());
        $this->assertEquals('2025-01-30', $membership->end_date->toDateString());
        $this->assertEquals(15000, $membership->price_paid);

        // Member should now be active
        $member->refresh();
        $this->assertEquals('active', $member->status);

        // A pending payment should have been created
        $this->assertDatabaseHas('gym_payments', [
            'member_id' => $member->id,
            'amount'    => 15000,
            'status'    => 'pending',
            'type'      => 'membership',
        ]);
    }

    public function test_assigning_new_plan_expires_previous_membership(): void
    {
        $member = Member::create(['full_name' => 'Ana García', 'status' => 'active']);

        $plan = MembershipPlan::create([
            'name' => 'Mensual', 'price' => 10000, 'duration_days' => 30, 'active' => true,
        ]);

        // First membership
        $this->service->assignPlan($member, $plan, ['start_date' => '2025-01-01']);

        // Assign a second plan — should expire the first
        $this->service->assignPlan($member, $plan, ['start_date' => '2025-02-01']);

        $activeMemberships = MemberMembership::where('member_id', $member->id)
            ->where('status', 'active')
            ->count();

        $this->assertEquals(1, $activeMemberships);
    }

    public function test_process_expirations_marks_expired_memberships(): void
    {
        $member = Member::create(['full_name' => 'Pedro López', 'status' => 'active']);

        MemberMembership::create([
            'member_id'          => $member->id,
            'membership_plan_id' => MembershipPlan::create([
                'name' => 'Test', 'price' => 1000, 'duration_days' => 1, 'active' => true,
            ])->id,
            'start_date'  => now()->subDays(10)->toDateString(),
            'end_date'    => now()->subDays(3)->toDateString(), // already expired
            'price_paid'  => 1000,
            'status'      => 'active',
        ]);

        $count = $this->service->processExpirations();

        $this->assertEquals(1, $count);
        $member->refresh();
        $this->assertEquals('expired', $member->status);
    }

    public function test_upcoming_expirations_returns_correct_count(): void
    {
        $plan = MembershipPlan::create([
            'name' => 'Test', 'price' => 1000, 'duration_days' => 30, 'active' => true,
        ]);

        for ($i = 0; $i < 3; $i++) {
            $member = Member::create(['full_name' => "Member {$i}", 'status' => 'active']);
            MemberMembership::create([
                'member_id'          => $member->id,
                'membership_plan_id' => $plan->id,
                'start_date'         => now()->subDays(25)->toDateString(),
                'end_date'           => now()->addDays(4)->toDateString(), // expiring in 4 days
                'price_paid'         => 1000,
                'status'             => 'active',
            ]);
        }

        $expirations = $this->service->getUpcomingExpirations(7);
        $this->assertCount(3, $expirations);
    }
}
