<?php

namespace App\Services\Gym;

use App\Models\GymPayment;
use App\Models\MemberMembership;
use App\Models\WebhookEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GymPaymentService
{
    /**
     * Confirm a pending payment manually.
     */
    public function confirm(GymPayment $payment, array $data = []): GymPayment
    {
        return DB::transaction(function () use ($payment, $data) {
            $payment->update([
                'status' => 'confirmed',
                'paid_at' => $data['paid_at'] ?? today(),
                'payment_method_id' => $data['payment_method_id'] ?? $payment->payment_method_id,
                'reference' => $data['reference'] ?? $payment->reference,
                'registered_by' => $data['registered_by'] ?? $payment->registered_by,
                'notes' => $data['notes'] ?? $payment->notes,
            ]);

            return $payment;
        });
    }

    /**
     * Process a Mercado Pago webhook event for gym payments.
     * Idempotent: safe to call multiple times with the same event.
     */
    public function processMercadoPagoWebhook(WebhookEvent $event): void
    {
        if ($event->isProcessed()) {
            return;
        }

        DB::transaction(function () use ($event) {
            $payload = $event->payload;
            $mpPaymentId = (string) ($payload['data']['id'] ?? '');
            $mpStatus = $payload['action'] ?? '';

            if (empty($mpPaymentId)) {
                $event->update(['processed_at' => now(), 'processing_error' => 'Missing payment id']);
                return;
            }

            $payment = GymPayment::where('mp_payment_id', $mpPaymentId)->first();

            if (! $payment) {
                Log::warning("GymPayment not found for MP id: {$mpPaymentId}");
                $event->update(['processed_at' => now(), 'processing_error' => 'Payment not found']);
                return;
            }

            $newStatus = match ($mpStatus) {
                'payment.created', 'payment.updated' => $this->mapMpStatus($payload['status'] ?? ''),
                default => null,
            };

            if ($newStatus && $payment->status !== $newStatus) {
                $payment->update([
                    'status' => $newStatus,
                    'mp_status' => $payload['status'] ?? null,
                    'paid_at' => $newStatus === 'confirmed' ? today() : $payment->paid_at,
                ]);
            }

            $event->update(['processed_at' => now()]);
        });
    }

    private function mapMpStatus(string $mpStatus): string
    {
        return match ($mpStatus) {
            'approved' => 'confirmed',
            'rejected', 'cancelled' => 'failed',
            'refunded', 'charged_back' => 'refunded',
            default => 'pending',
        };
    }
}
