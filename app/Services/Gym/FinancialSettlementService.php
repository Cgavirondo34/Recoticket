<?php

namespace App\Services\Gym;

use App\Models\Expense;
use App\Models\FinancialSettlement;
use App\Models\GymPayment;
use App\Models\Partner;
use App\Models\ReservationPayment;
use App\Models\SettlementItem;
use Carbon\Carbon;

class FinancialSettlementService
{
    /**
     * Generate (or regenerate) a monthly settlement.
     */
    public function generate(int $year, int $month, ?int $tenantId = null): FinancialSettlement
    {
        // ── Income ────────────────────────────────────────────────────────────
        $gymIncome = GymPayment::where('status', 'paid')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->whereYear('paid_at', $year)->whereMonth('paid_at', $month)
            ->sum('amount');

        $fieldIncome = ReservationPayment::whereHas('reservation', function ($q) use ($year, $month, $tenantId) {
                $q->whereYear('reservation_date', $year)
                  ->whereMonth('reservation_date', $month)
                  ->when($tenantId, fn($q2) => $q2->where('tenant_id', $tenantId));
            })
            ->sum('amount');

        $totalIncome = $gymIncome + $fieldIncome;

        // ── Expenses ──────────────────────────────────────────────────────────
        $expenses = Expense::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->whereYear('expense_date', $year)->whereMonth('expense_date', $month)
            ->get();

        $totalExpenses = $expenses->sum('amount');
        $netIncome     = $totalIncome - $totalExpenses;

        // ── Partner distributions ─────────────────────────────────────────────
        $partners    = Partner::where('active', true)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->get();

        $distributions = $partners->map(function (Partner $partner) use ($gymIncome, $fieldIncome, $expenses) {
            $gymShare   = $gymIncome   * ($partner->gym_percentage   / 100);
            $fieldShare = $fieldIncome * ($partner->field_percentage / 100);

            // Shared expenses split equally
            $sharedExp   = $expenses->where('business_unit', 'shared')->sum('amount');
            $expenseShare = $partners->count() > 0 ? $sharedExp / $partners->count() : 0;

            return [
                'partner_id'    => $partner->id,
                'partner_name'  => $partner->name,
                'gym_income'    => round($gymShare, 2),
                'field_income'  => round($fieldShare, 2),
                'expense_share' => round($expenseShare, 2),
                'net_earning'   => round($gymShare + $fieldShare - $expenseShare, 2),
            ];
        })->toArray();

        // ── Persist ───────────────────────────────────────────────────────────
        $settlement = FinancialSettlement::updateOrCreate(
            ['tenant_id' => $tenantId, 'year' => $year, 'month' => $month],
            [
                'gym_income'             => $gymIncome,
                'field_income'           => $fieldIncome,
                'total_income'           => $totalIncome,
                'total_expenses'         => $totalExpenses,
                'net_income'             => $netIncome,
                'partner_distributions'  => $distributions,
                'status'                 => 'draft',
            ]
        );

        // Rebuild settlement items
        $settlement->items()->delete();
        foreach ($expenses as $exp) {
            SettlementItem::create([
                'financial_settlement_id' => $settlement->id,
                'type'                    => 'expense',
                'label'                   => $exp->description,
                'amount'                  => -$exp->amount,
            ]);
        }

        return $settlement->load('items');
    }
}
