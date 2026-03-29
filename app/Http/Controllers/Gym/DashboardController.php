<?php

namespace App\Http\Controllers\Gym;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\GymPayment;
use App\Models\Member;
use App\Models\Reservation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();

        $stats = [
            'members_active'    => Member::active()->count(),
            'members_expired'   => Member::expired()->count(),
            'members_suspended' => Member::suspended()->count(),

            // Payments this month
            'monthly_income'    => GymPayment::paid()
                ->whereYear('paid_at', $today->year)
                ->whereMonth('paid_at', $today->month)
                ->sum('amount'),

            // Expenses this month
            'monthly_expenses'  => Expense::whereYear('expense_date', $today->year)
                ->whereMonth('expense_date', $today->month)
                ->sum('amount'),

            // Upcoming payments (members expiring in 7 days)
            'upcoming_payments' => Member::active()
                ->whereNotNull('membership_expires_at')
                ->whereBetween('membership_expires_at', [$today, $today->copy()->addDays(7)])
                ->count(),

            // Overdue
            'overdue_payments'  => Member::expired()->count(),

            // Today's reservations
            'reservations_today' => Reservation::confirmed()->forDate($today)->count(),

            // This month's field income
            'field_income'      => \App\Models\ReservationPayment::whereHas('reservation', function ($q) use ($today) {
                    $q->whereYear('reservation_date', $today->year)
                      ->whereMonth('reservation_date', $today->month);
                })->sum('amount'),
        ];

        $stats['net_result'] = $stats['monthly_income'] + $stats['field_income'] - $stats['monthly_expenses'];

        // Members expiring soon (3 days)
        $dueSoon = Member::active()
            ->with('currentPlan')
            ->whereNotNull('membership_expires_at')
            ->whereBetween('membership_expires_at', [$today, $today->copy()->addDays(3)])
            ->orderBy('membership_expires_at')
            ->limit(5)
            ->get();

        // Recent payments
        $recentPayments = GymPayment::with('member')
            ->latest()
            ->limit(5)
            ->get();

        // Upcoming reservations
        $upcomingReservations = Reservation::with('timeSlot')
            ->confirmed()
            ->where('reservation_date', '>=', $today)
            ->orderBy('reservation_date')
            ->orderBy('field_time_slot_id')
            ->limit(5)
            ->get();

        return view('gym.dashboard', compact('stats', 'dueSoon', 'recentPayments', 'upcomingReservations'));
    }
}
