<?php

namespace App\Jobs\Gym;

use App\Models\GymPayment;
use App\Services\Gym\MercadoPagoService;
use App\Services\Gym\PaymentService;
use App\Services\Gym\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Processes an incoming Mercado Pago webhook notification.
 * Dispatched by the GymPaymentController webhook endpoint.
 */
class ProcessMercadoPagoWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(protected array $payload) {}

    public function handle(
        MercadoPagoService $mpService,
        PaymentService $paymentService,
        WhatsAppService $whatsApp
    ): void {
        $mpData = $mpService->processWebhook($this->payload);
        if (!$mpData) {
            return; // Not a payment notification
        }

        $externalRef = $mpData['external_reference'] ?? null;
        $mpStatus    = $mpData['status'] ?? null;
        $mpId        = (string) ($mpData['id'] ?? '');

        if (!$externalRef || $mpStatus !== 'approved') {
            return;
        }

        // External reference format: "gym_payment_{id}"
        if (!str_starts_with($externalRef, 'gym_payment_')) {
            return;
        }

        $paymentId = (int) str_replace('gym_payment_', '', $externalRef);
        $payment   = GymPayment::find($paymentId);

        if (!$payment || $payment->status === 'paid') {
            return;
        }

        $paymentService->confirmPayment($payment, $mpId);
        $payment->load('member');

        // Send confirmation notification
        if ($payment->member?->whatsapp) {
            $whatsApp->sendFromTemplate('payment_confirmed', $payment->member->whatsapp, [
                'nombre' => $payment->member->full_name,
                'monto'  => '$' . number_format($payment->amount, 2, ',', '.'),
            ], $payment->tenant_id);
        }

        Log::info("MercadoPago payment confirmed: payment #{$paymentId}, mp_id={$mpId}");
    }
}
