<?php

namespace App\Services\Gym;

use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\MembershipPlan;
use Carbon\Carbon;

class MemberService
{
    /**
     * Assign a plan to a member, creating a new MemberMembership record.
     */
    public function assignPlan(Member $member, MembershipPlan $plan, ?string $startsAt = null, bool $autoRenew = false): MemberMembership
    {
        $starts = $startsAt ? Carbon::parse($startsAt) : Carbon::today();
        $expires = $starts->copy()->addDays($plan->duration_days);

        // Deactivate previous active memberships
        $member->memberships()->where('status', 'active')->update(['status' => 'expired']);

        $membership = MemberMembership::create([
            'tenant_id'          => $member->tenant_id,
            'member_id'          => $member->id,
            'membership_plan_id' => $plan->id,
            'starts_at'          => $starts,
            'expires_at'         => $expires,
            'auto_renew'         => $autoRenew,
            'status'             => 'active',
        ]);

        $member->update([
            'current_plan_id'        => $plan->id,
            'membership_expires_at'  => $expires,
            'status'                 => 'active',
        ]);

        return $membership;
    }

    /**
     * Update member statuses based on expiration dates.
     * Called by the scheduled task.
     */
    public function syncExpiredStatuses(): int
    {
        $count = Member::where('status', 'active')
            ->whereNotNull('membership_expires_at')
            ->where('membership_expires_at', '<', Carbon::today())
            ->update(['status' => 'expired']);

        // Sync membership records
        MemberMembership::where('status', 'active')
            ->where('expires_at', '<', Carbon::today())
            ->update(['status' => 'expired']);

        return $count;
    }

    /**
     * Return members whose membership expires within $days days.
     */
    public function getDueSoon(int $days = 3): \Illuminate\Database\Eloquent\Collection
    {
        return Member::where('status', 'active')
            ->whereNotNull('membership_expires_at')
            ->whereBetween('membership_expires_at', [
                Carbon::today(),
                Carbon::today()->addDays($days),
            ])
            ->get();
    }

    /**
     * Return overdue members.
     */
    public function getOverdue(): \Illuminate\Database\Eloquent\Collection
    {
        return Member::where('status', 'expired')->get();
    }
}
