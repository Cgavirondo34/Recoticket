<?php

namespace App\Http\Controllers\Gym;

use App\Http\Controllers\Controller;
use App\Models\FinancialSettlement;
use App\Services\Gym\FinancialSettlementService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function __construct(protected FinancialSettlementService $settlementService) {}

    public function index()
    {
        $settlements = FinancialSettlement::orderByDesc('year')->orderByDesc('month')->paginate(12);
        return view('gym.settlement.index', compact('settlements'));
    }

    public function show(int $year, int $month)
    {
        $settlement = FinancialSettlement::where('year', $year)->where('month', $month)->first();

        if (!$settlement) {
            // Generate on the fly
            $settlement = $this->settlementService->generate($year, $month);
        }

        $settlement->load('items');

        return view('gym.settlement.show', compact('settlement', 'year', 'month'));
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'year'  => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $settlement = $this->settlementService->generate($data['year'], $data['month']);

        return redirect()->route('gym.settlement.show', ['year' => $data['year'], 'month' => $data['month']])
            ->with('success', "Liquidación {$data['month']}/{$data['year']} generada.");
    }

    public function confirm(int $year, int $month)
    {
        $settlement = FinancialSettlement::where('year', $year)->where('month', $month)->firstOrFail();
        $settlement->update(['status' => 'confirmed']);

        return back()->with('success', 'Liquidación confirmada.');
    }
}
