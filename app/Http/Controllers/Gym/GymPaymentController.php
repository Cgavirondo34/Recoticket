<?php

namespace App\Http\Controllers\Gym;

use App\Http\Controllers\Controller;
use App\Jobs\Gym\ProcessMercadoPagoWebhook;
use App\Models\GymPayment;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\PaymentMethod;
use App\Services\Gym\MercadoPagoService;
use App\Services\Gym\PaymentService;
use App\Services\Gym\WhatsAppService;
use Illuminate\Http\Request;

class GymPaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected WhatsAppService $whatsApp
    ) {}

    public function index(Request $request)
    {
        $query = GymPayment::with(['member', 'paymentMethod'])->latest('paid_at');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($member = $request->input('member_id')) {
            $query->where('member_id', $member);
        }

        $payments = $query->paginate(20)->withQueryString();
        $members  = Member::orderBy('full_name')->get(['id', 'full_name']);

        return view('gym.payments.index', compact('payments', 'members'));
    }

    public function create(Request $request)
    {
        $members        = Member::active()->orderBy('full_name')->get(['id', 'full_name']);
        $paymentMethods = PaymentMethod::where('active', true)->get();
        $selectedMember = $request->integer('member_id') ? Member::find($request->integer('member_id')) : null;

        return view('gym.payments.create', compact('members', 'paymentMethods', 'selectedMember'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id'            => 'required|exists:members,id',
            'member_membership_id' => 'nullable|exists:member_memberships,id',
            'payment_method_id'    => 'nullable|exists:payment_methods,id',
            'amount'               => 'required|numeric|min:0.01',
            'paid_at'              => 'required|date',
            'reference'            => 'nullable|string|max:255',
            'notes'                => 'nullable|string',
        ]);

        $payment = $this->paymentService->registerPayment($data);

        // Send WhatsApp confirmation
        $member = $payment->member;
        if ($member?->whatsapp) {
            $this->whatsApp->sendFromTemplate('payment_confirmed', $member->whatsapp, [
                'nombre' => $member->full_name,
                'monto'  => '$' . number_format($payment->amount, 2, ',', '.'),
            ], $payment->tenant_id);
        }

        return redirect()->route('gym.payments.index')
            ->with('success', 'Pago registrado.');
    }

    public function show(GymPayment $payment)
    {
        $payment->load(['member', 'membership.plan', 'paymentMethod']);
        return view('gym.payments.show', compact('payment'));
    }

    /**
     * Mercado Pago webhook endpoint.
     */
    public function webhook(Request $request)
    {
        ProcessMercadoPagoWebhook::dispatch($request->all());
        return response()->json(['status' => 'ok']);
    }

    /**
     * Generate a Mercado Pago payment link for a pending payment.
     */
    public function mercadoPagoLink(GymPayment $payment, MercadoPagoService $mpService)
    {
        try {
            $preference = $this->paymentService->createMercadoPagoPreference($payment, $mpService);
            return redirect($preference['init_point']);
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al generar link de pago: ' . $e->getMessage());
        }
    }
}
