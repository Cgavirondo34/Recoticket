<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Models\GymPayment;
use App\Models\Member;
use App\Services\Gym\GymPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GymPaymentController extends Controller
{
    public function __construct(private readonly GymPaymentService $service) {}

    /** GET /api/gym/payments */
    public function index(Request $request): JsonResponse
    {
        $query = GymPayment::with(['member', 'membership.plan', 'paymentMethod'])
            ->when($request->member_id, fn($q, $id) => $q->where('member_id', $id))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->from, fn($q, $d) => $q->where('paid_at', '>=', $d))
            ->when($request->to, fn($q, $d) => $q->where('paid_at', '<=', $d))
            ->orderByDesc('created_at');

        return response()->json($query->paginate($request->per_page ?? 20));
    }

    /** GET /api/gym/members/{member}/payments */
    public function memberPayments(Member $member): JsonResponse
    {
        $payments = GymPayment::with(['membership.plan', 'paymentMethod', 'registeredBy'])
            ->where('member_id', $member->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($payments);
    }

    /** POST /api/gym/payments */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'member_id'             => 'required|integer|exists:members,id',
            'member_membership_id'  => 'nullable|integer|exists:member_memberships,id',
            'payment_method_id'     => 'nullable|integer|exists:payment_methods,id',
            'amount'                => 'required|numeric|min:0',
            'type'                  => 'nullable|in:membership,extra,adjustment',
            'reference'             => 'nullable|string|max:255',
            'paid_at'               => 'nullable|date',
            'notes'                 => 'nullable|string',
        ]);

        $validated['tenant_id'] = $request->user()->tenant_id;
        $validated['registered_by'] = $request->user()->id;
        $validated['status'] = 'pending';
        $validated['type'] = $validated['type'] ?? 'membership';

        $payment = GymPayment::create($validated);

        return response()->json($payment->load('member', 'paymentMethod'), 201);
    }

    /** POST /api/gym/payments/{payment}/confirm */
    public function confirm(Request $request, GymPayment $payment): JsonResponse
    {
        $validated = $request->validate([
            'payment_method_id' => 'nullable|integer|exists:payment_methods,id',
            'paid_at'           => 'nullable|date',
            'reference'         => 'nullable|string|max:255',
            'notes'             => 'nullable|string',
        ]);

        $validated['registered_by'] = $request->user()->id;

        $payment = $this->service->confirm($payment, $validated);

        return response()->json($payment->load('member', 'paymentMethod'));
    }
}
