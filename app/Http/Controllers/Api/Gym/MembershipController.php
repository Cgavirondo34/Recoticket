<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\MembershipPlan;
use App\Services\Gym\MembershipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function __construct(private readonly MembershipService $service) {}

    /** GET /api/gym/membership-plans */
    public function plans(Request $request): JsonResponse
    {
        $plans = MembershipPlan::where('active', true)->orderBy('sort_order')->get();
        return response()->json($plans);
    }

    /** POST /api/gym/members/{member}/memberships */
    public function assign(Request $request, Member $member): JsonResponse
    {
        $validated = $request->validate([
            'membership_plan_id' => 'required|integer|exists:membership_plans,id',
            'start_date'         => 'nullable|date',
            'price_paid'         => 'nullable|numeric|min:0',
            'payment_method_id'  => 'nullable|integer|exists:payment_methods,id',
            'auto_renew'         => 'boolean',
            'notes'              => 'nullable|string',
        ]);

        $plan = MembershipPlan::findOrFail($validated['membership_plan_id']);
        $validated['created_by'] = $request->user()->id;

        $membership = $this->service->assignPlan($member, $plan, $validated);

        return response()->json($membership->load('plan'), 201);
    }

    /** GET /api/gym/members/{member}/memberships */
    public function history(Member $member): JsonResponse
    {
        $memberships = MemberMembership::with('plan')
            ->where('member_id', $member->id)
            ->orderByDesc('start_date')
            ->get();

        return response()->json($memberships);
    }

    /** GET /api/gym/memberships/upcoming-expirations */
    public function upcomingExpirations(Request $request): JsonResponse
    {
        $days = (int) ($request->days ?? 7);
        $tenantId = $request->user()->tenant_id;
        $expirations = $this->service->getUpcomingExpirations($days, $tenantId);

        return response()->json($expirations);
    }
}
