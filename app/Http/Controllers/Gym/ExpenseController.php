<?php

namespace App\Http\Controllers\Gym;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->integer('year',  Carbon::today()->year);
        $month = $request->integer('month', Carbon::today()->month);

        $query = Expense::with(['category', 'paymentMethod'])
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month)
            ->orderByDesc('expense_date');

        if ($unit = $request->input('business_unit')) {
            $query->where('business_unit', $unit);
        }
        if ($category = $request->integer('category_id')) {
            $query->where('expense_category_id', $category);
        }

        $expenses   = $query->paginate(20)->withQueryString();
        $categories = ExpenseCategory::orderBy('name')->get();

        $totals = [
            'gym'    => Expense::gym()->forMonth($year, $month)->sum('amount'),
            'field'  => Expense::field()->forMonth($year, $month)->sum('amount'),
            'shared' => Expense::shared()->forMonth($year, $month)->sum('amount'),
            'total'  => Expense::whereYear('expense_date', $year)->whereMonth('expense_date', $month)->sum('amount'),
        ];

        // Category breakdown for current month
        $byCategory = Expense::with('category')
            ->forMonth($year, $month)
            ->get()
            ->groupBy('expense_category_id')
            ->map(function ($items) {
                return [
                    'label'  => $items->first()->category?->name ?? 'Sin categoría',
                    'color'  => $items->first()->category?->color ?? '#6b7280',
                    'amount' => $items->sum('amount'),
                ];
            })->values();

        return view('gym.expenses.index', compact('expenses', 'categories', 'totals', 'byCategory', 'year', 'month'));
    }

    public function create()
    {
        $categories     = ExpenseCategory::orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('active', true)->get();
        return view('gym.expenses.create', compact('categories', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'payment_method_id'   => 'nullable|exists:payment_methods,id',
            'description'         => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0.01',
            'expense_date'        => 'required|date',
            'business_unit'       => 'required|in:gym,field,shared',
            'notes'               => 'nullable|string',
        ]);

        Expense::create($data);

        return redirect()->route('gym.expenses.index')
            ->with('success', 'Gasto registrado.');
    }

    public function edit(Expense $expense)
    {
        $categories     = ExpenseCategory::orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('active', true)->get();
        return view('gym.expenses.edit', compact('expense', 'categories', 'paymentMethods'));
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'payment_method_id'   => 'nullable|exists:payment_methods,id',
            'description'         => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0.01',
            'expense_date'        => 'required|date',
            'business_unit'       => 'required|in:gym,field,shared',
            'notes'               => 'nullable|string',
        ]);

        $expense->update($data);

        return redirect()->route('gym.expenses.index')
            ->with('success', 'Gasto actualizado.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('gym.expenses.index')
            ->with('success', 'Gasto eliminado.');
    }
}
