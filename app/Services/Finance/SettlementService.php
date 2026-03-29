<?php

namespace App\Services\Finance;

use App\Models\BusinessPartner;
use App\Models\Expense;
use App\Models\FieldReservation;
use App\Models\FinancialSettlement;
use App\Models\GymPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SettlementService
{
    /**
     * Calculate and persist (or refresh) the settlement for a given month.
     * Idempotent: calling again on draft recalculates. Closed settlements cannot be refreshed.
     */
    public function calculate(?int $tenantId, int $year, int $month): FinancialSettlement
    {
        $existing = FinancialSettlement::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if ($existing && $existing->isClosed()) {
            throw new \RuntimeException("Settlement for {$year}-{$month} is already closed and cannot be recalculated.");
        }

        return DB::transaction(function () use ($tenantId, $year, $month, $existing) {
            $start = Carbon::create($year, $month, 1)->startOfDay()->toDateString();
            $end = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

            // Income: confirmed gym payments
            $gymIncome = GymPayment::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->where('status', 'confirmed')
                ->whereBetween('paid_at', [$start, $end])
                ->sum('amount');

            // Income: paid field reservations
            $fieldIncome = FieldReservation::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->where('payment_status', 'paid')
                ->whereBetween('reservation_date', [$start, $end])
                ->sum('price');

            $totalIncome = $gymIncome + $fieldIncome;

            // Expenses by unit
            $gymExpenses = Expense::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->where('business_unit', 'gym')
                ->whereBetween('expense_date', [$start, $end])
                ->sum('amount');

            $fieldExpenses = Expense::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->where('business_unit', 'football_field')
                ->whereBetween('expense_date', [$start, $end])
                ->sum('amount');

            $sharedExpenses = Expense::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->where('business_unit', 'shared')
                ->whereBetween('expense_date', [$start, $end])
                ->sum('amount');

            $totalExpenses = $gymExpenses + $fieldExpenses + $sharedExpenses;

            // Net per unit (shared expenses split 50/50 between gym and field by default)
            $sharedHalf = $sharedExpenses / 2;
            $gymNet = $gymIncome - $gymExpenses - $sharedHalf;
            $fieldNet = $fieldIncome - $fieldExpenses - $sharedHalf;
            $totalNet = $gymNet + $fieldNet;

            // Partner earnings
            $partners = BusinessPartner::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->where('active', true)->get();
            $partnerEarnings = [];

            foreach ($partners as $partner) {
                $gymShare = round($gymNet * ($partner->gym_percentage / 100), 2);
                $fieldShare = round($fieldNet * ($partner->field_percentage / 100), 2);
                $partnerEarnings[$partner->id] = [
                    'name' => $partner->display_name,
                    'gym_percentage' => $partner->gym_percentage,
                    'field_percentage' => $partner->field_percentage,
                    'gym_share' => $gymShare,
                    'field_share' => $fieldShare,
                    'total' => $gymShare + $fieldShare,
                ];
            }

            $data = [
                'tenant_id' => $tenantId,
                'year' => $year,
                'month' => $month,
                'gym_income' => $gymIncome,
                'field_income' => $fieldIncome,
                'total_income' => $totalIncome,
                'gym_expenses' => $gymExpenses,
                'field_expenses' => $fieldExpenses,
                'shared_expenses' => $sharedExpenses,
                'total_expenses' => $totalExpenses,
                'gym_net' => $gymNet,
                'field_net' => $fieldNet,
                'total_net' => $totalNet,
                'partner_earnings' => $partnerEarnings,
                'status' => 'draft',
            ];

            if ($existing) {
                $existing->update($data);
                return $existing->fresh();
            }

            return FinancialSettlement::create($data);
        });
    }

    /**
     * Close a settlement — marks it as final and immutable.
     */
    public function close(FinancialSettlement $settlement, int $closedBy, ?string $notes = null): FinancialSettlement
    {
        if ($settlement->isClosed()) {
            throw new \RuntimeException("Settlement is already closed.");
        }

        $settlement->update([
            'status' => 'closed',
            'closed_by' => $closedBy,
            'closed_at' => now(),
            'notes' => $notes ?? $settlement->notes,
        ]);

        return $settlement->fresh();
    }
}
