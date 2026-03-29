<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialSettlement;
use App\Services\Finance\SettlementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function __construct(private readonly SettlementService $service) {}

    /**
     * GET /api/finance/settlements
     * List all settlements for the tenant, ordered by most recent.
     */
    public function index(Request $request): JsonResponse
    {
        $settlements = FinancialSettlement::where('tenant_id', $request->user()->tenant_id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return response()->json($settlements);
    }

    /**
     * POST /api/finance/settlements/calculate
     * Calculate (or recalculate) the settlement for a given month.
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year'  => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|between:1,12',
        ]);

        try {
            $settlement = $this->service->calculate(
                $request->user()->tenant_id,
                $validated['year'],
                $validated['month']
            );
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($settlement);
    }

    /**
     * GET /api/finance/settlements/{year}/{month}
     * Retrieve a specific settlement.
     */
    public function show(Request $request, int $year, int $month): JsonResponse
    {
        $settlement = FinancialSettlement::where('tenant_id', $request->user()->tenant_id)
            ->where('year', $year)
            ->where('month', $month)
            ->firstOrFail();

        return response()->json($settlement);
    }

    /**
     * POST /api/finance/settlements/{year}/{month}/close
     * Mark a settlement as closed (irreversible).
     */
    public function close(Request $request, int $year, int $month): JsonResponse
    {
        $settlement = FinancialSettlement::where('tenant_id', $request->user()->tenant_id)
            ->where('year', $year)
            ->where('month', $month)
            ->firstOrFail();

        $validated = $request->validate(['notes' => 'nullable|string']);

        try {
            $settlement = $this->service->close($settlement, $request->user()->id, $validated['notes'] ?? null);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($settlement);
    }
}
