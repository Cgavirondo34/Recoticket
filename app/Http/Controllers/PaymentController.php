<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function checkout(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);

        $accessToken = config('services.mercadopago.access_token');

        if (!$accessToken) {
            if (app()->environment('local', 'testing')) {
                $this->orderService->approveOrder($order);
                return redirect()->route('payment.success')->with('order_id', $order->id)
                    ->with('success', 'Pago simulado exitoso (modo demo).');
            }
            return back()->with('error', 'El sistema de pagos no está configurado. Contactá al administrador.');
        }

        $order->load('event', 'orderItems.ticketType');

        $items = $order->orderItems->map(fn($item) => [
            'title'      => $item->ticketType->name . ' — ' . $order->event->title,
            'quantity'   => $item->quantity,
            'unit_price' => (float) $item->unit_price,
            'currency_id' => 'ARS',
        ])->toArray();

        $response = Http::withToken($accessToken)->post('https://api.mercadopago.com/checkout/preferences', [
            'items'              => $items,
            'external_reference' => $order->order_number,
            'back_urls'          => [
                'success' => route('payment.success'),
                'failure' => route('payment.failure'),
                'pending' => route('payment.success'),
            ],
            'auto_return' => 'approved',
        ]);

        if ($response->failed()) {
            Log::error('MP preference error', $response->json());
            return back()->with('error', 'Error al crear preferencia de pago.');
        }

        $data = $response->json();
        Payment::create([
            'order_id'         => $order->id,
            'mp_preference_id' => $data['id'],
            'status'           => 'pending',
            'amount'           => $order->total,
            'currency'         => 'ARS',
            'raw_response'     => $data,
        ]);

        return redirect($data['init_point']);
    }

    public function success(Request $request)
    {
        $orderNumber = $request->get('external_reference');
        $order = null;

        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)->first();
            if ($order && $order->status === 'pending') {
                $this->orderService->approveOrder($order);
            }
        }

        return view('payment.success', compact('order'));
    }

    public function failure(Request $request)
    {
        $orderNumber = $request->get('external_reference');
        $order = $orderNumber ? Order::where('order_number', $orderNumber)->first() : null;
        return view('payment.failure', compact('order'));
    }

    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::info('MP Webhook', $payload);

        if (($payload['type'] ?? '') === 'payment') {
            $paymentId = $payload['data']['id'] ?? null;
            if ($paymentId) {
                $accessToken = config('services.mercadopago.access_token');
                $response = Http::withToken($accessToken)->get("https://api.mercadopago.com/v1/payments/{$paymentId}");
                if ($response->ok()) {
                    $data = $response->json();
                    $orderNumber = $data['external_reference'] ?? null;
                    if ($orderNumber) {
                        $order = Order::where('order_number', $orderNumber)->first();
                        if ($order) {
                            Payment::updateOrCreate(
                                ['order_id' => $order->id],
                                [
                                    'mp_payment_id' => $paymentId,
                                    'status'        => $data['status'],
                                    'payment_type'  => $data['payment_type_id'] ?? null,
                                    'amount'        => $data['transaction_amount'] ?? $order->total,
                                    'currency'      => $data['currency_id'] ?? 'ARS',
                                    'raw_response'  => $data,
                                ]
                            );
                            if ($data['status'] === 'approved' && $order->status === 'pending') {
                                $this->orderService->approveOrder($order);
                            }
                        }
                    }
                }
            }
        }

        return response()->json(['ok' => true]);
    }
}
