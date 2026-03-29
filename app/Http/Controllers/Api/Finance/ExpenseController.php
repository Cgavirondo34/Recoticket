<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /** GET /api/finance/expenses */
    public function index(Request $request): JsonResponse
    {
        $query = Expense::with(['category', 'paymentMethod', 'registeredBy:id,name'])
            ->when($request->from, fn($q, $d) => $q->where('expense_date', '>=', $d))
            ->when($request->to, fn($q, $d) => $q->where('expense_date', '<=', $d))
            ->when($request->business_unit, fn($q, $u) => $q->where('business_unit', $u))
            ->when($request->category_id, fn($q, $id) => $q->where('expense_category_id', $id))
            ->orderByDesc('expense_date');

        return response()->json($query->paginate($request->per_page ?? 20));
    }

    /** POST /api/finance/expenses */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|integer|exists:expense_categories,id',
            'payment_method_id'   => 'nullable|integer|exists:payment_methods,id',
            'description'         => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0.01',
            'business_unit'       => 'required|in:gym,football_field,shared',
            'expense_date'        => 'required|date',
            'notes'               => 'nullable|string',
        ]);

        $validated['tenant_id'] = $request->user()->tenant_id;
        $validated['registered_by'] = $request->user()->id;

        $expense = Expense::create($validated);

        return response()->json($expense->load('category', 'paymentMethod'), 201);
    }

    /** GET /api/finance/expenses/{expense} */
    public function show(Expense $expense): JsonResponse
    {
        return response()->json($expense->load(['category', 'paymentMethod', 'registeredBy:id,name']));
    }

    /** PUT /api/finance/expenses/{expense} */
    public function update(Request $request, Expense $expense): JsonResponse
    {
        $validated = $request->validate([
            'expense_category_id' => 'sometimes|integer|exists:expense_categories,id',
            'payment_method_id'   => 'nullable|integer|exists:payment_methods,id',
            'description'         => 'sometimes|string|max:255',
            'amount'              => 'sometimes|numeric|min:0.01',
            'business_unit'       => 'sometimes|in:gym,football_field,shared',
            'expense_date'        => 'sometimes|date',
            'notes'               => 'nullable|string',
            'adjustment_reason'   => 'nullable|string',
        ]);

        if (isset($validated['adjustment_reason'])) {
            $validated['is_adjustment'] = true;
            $validated['adjusted_by'] = $request->user()->id;
        }

        $expense->update($validated);

        return response()->json($expense->fresh()->load('category', 'paymentMethod'));
    }

    /** DELETE /api/finance/expenses/{expense} */
    public function destroy(Expense $expense): JsonResponse
    {
        $expense->delete();
        return response()->json(null, 204);
    }

    /** GET /api/finance/expense-categories */
    public function categories(Request $request): JsonResponse
    {
        $categories = ExpenseCategory::where('active', true)
            ->when($request->business_unit, fn($q, $u) => $q->where('business_unit', $u))
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }
}
