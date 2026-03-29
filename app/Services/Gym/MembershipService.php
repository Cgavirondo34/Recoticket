<?php

namespace App\Services\Gym;

use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\MembershipPlan;
use App\Models\GymPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MembershipService
{
    /**
     * Assign a membership plan to a member and create the membership record.
     * Creates an associated pending payment.
     */
    public function assignPlan(Member $member, MembershipPlan $plan, array $data = []): MemberMembership
    {
        return DB::transaction(function () use ($member, $plan, $data) {
            $startDate = Carbon::parse($data['start_date'] ?? today());
            $endDate = $startDate->copy()->addDays($plan->duration_days - 1);

            // Deactivate any existing active membership
            MemberMembership::where('member_id', $member->id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);

            $membership = MemberMembership::create([
                'tenant_id' => $member->tenant_id,
                'member_id' => $member->id,
                'membership_plan_id' => $plan->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'price_paid' => $data['price_paid'] ?? $plan->price,
                'status' => 'active',
                'auto_renew' => $data['auto_renew'] ?? false,
                'created_by' => $data['created_by'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Create pending payment
            GymPayment::create([
                'tenant_id' => $member->tenant_id,
                'member_id' => $member->id,
                'member_membership_id' => $membership->id,
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'amount' => $membership->price_paid,
                'status' => 'pending',
                'type' => 'membership',
                'registered_by' => $data['created_by'] ?? null,
            ]);

            // Activate member if not already active
            if ($member->status !== 'active') {
                $member->update(['status' => 'active']);
            }

            return $membership;
        });
    }

    /**
     * Mark expired memberships and update member statuses.
     * Called by scheduler daily.
     */
    public function processExpirations(?int $tenantId = null): int
    {
        $query = MemberMembership::where('status', 'active')
            ->where('end_date', '<', today());

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $expired = $query->get();
        $count = 0;

        foreach ($expired as $membership) {
            DB::transaction(function () use ($membership, &$count) {
                $membership->update(['status' => 'expired']);

                // Check if member has another active membership
                $hasActive = MemberMembership::where('member_id', $membership->member_id)
                    ->where('status', 'active')
                    ->exists();

                if (! $hasActive) {
                    Member::where('id', $membership->member_id)
                        ->where('status', 'active')
                        ->update(['status' => 'expired']);
                }

                $count++;
            });
        }

        return $count;
    }

    /**
     * Get members with memberships expiring within N days.
     */
    public function getUpcomingExpirations(int $days = 7, ?int $tenantId = null): \Illuminate\Support\Collection
    {
        $query = MemberMembership::with('member', 'plan')
            ->where('status', 'active')
            ->whereBetween('end_date', [today(), today()->addDays($days)]);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return $query->orderBy('end_date')->get();
    }
}
