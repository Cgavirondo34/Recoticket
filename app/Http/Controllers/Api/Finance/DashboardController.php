<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\GymPayment;
use App\Models\FieldReservation;
use App\Models\Member;
use App\Models\MemberMembership;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * GET /api/finance/dashboard
     * Returns KPI summary for the dashboard.
     */
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $kpis = [
            'members' => [
                'active'    => Member::where('tenant_id', $tenantId)->where('status', 'active')->count(),
                'expired'   => Member::where('tenant_id', $tenantId)->where('status', 'expired')->count(),
                'suspended' => Member::where('tenant_id', $tenantId)->where('status', 'suspended')->count(),
                'total'     => Member::where('tenant_id', $tenantId)->whereNotIn('status', ['inactive'])->count(),
            ],
            'payments' => [
                'pending'      => GymPayment::where('tenant_id', $tenantId)->where('status', 'pending')->count(),
                'monthly_income' => GymPayment::where('tenant_id', $tenantId)
                    ->where('status', 'confirmed')
                    ->whereBetween('paid_at', [$monthStart, $monthEnd])
                    ->sum('amount'),
            ],
            'memberships' => [
                'expiring_7_days' => MemberMembership::where('tenant_id', $tenantId)
                    ->where('status', 'active')
                    ->whereBetween('end_date', [today(), today()->addDays(7)])
                    ->count(),
            ],
            'field' => [
                'reservations_today'  => FieldReservation::where('tenant_id', $tenantId)
                    ->whereDate('reservation_date', today())
                    ->where('status', '!=', 'cancelled')
                    ->count(),
                'monthly_field_income' => FieldReservation::where('tenant_id', $tenantId)
                    ->where('payment_status', 'paid')
                    ->whereBetween('reservation_date', [$monthStart, $monthEnd])
                    ->sum('price'),
            ],
        ];

        return response()->json($kpis);
    }
}
