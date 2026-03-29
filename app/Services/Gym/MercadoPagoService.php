<?php

namespace App\Services\Gym;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Mercado Pago integration service.
 *
 * Handles preference creation, payment status queries, and webhook processing.
 * Access token is read from config/services.php under the "mercadopago" key.
 */
class MercadoPagoService
{
    protected string $accessToken;
    protected string $baseUrl = 'https://api.mercadopago.com';

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token', '');
    }

    /**
     * Create a payment preference.
     *
     * @param array $item  ['title', 'quantity', 'unit_price', 'external_reference']
     * @return array       Preference data with 'id' and 'init_point'
     */
    public function createPreference(array $item): array
    {
        $payload = [
            'items' => [
                [
                    'title'       => $item['title'],
                    'quantity'    => $item['quantity'] ?? 1,
                    'unit_price'  => (float) $item['unit_price'],
                    'currency_id' => config('services.mercadopago.currency', 'ARS'),
                ],
            ],
            'external_reference' => $item['external_reference'] ?? null,
            'back_urls' => [
                'success' => config('services.mercadopago.success_url', url('/gym/payments/success')),
                'failure' => config('services.mercadopago.failure_url', url('/gym/payments/failure')),
                'pending' => config('services.mercadopago.pending_url', url('/gym/payments/pending')),
            ],
            'auto_return' => 'approved',
            'notification_url' => url('/gym/payments/webhook'),
        ];

        $response = Http::withToken($this->accessToken)
            ->post("{$this->baseUrl}/checkout/preferences", $payload);

        if (!$response->successful()) {
            Log::error('MercadoPago preference error: ' . $response->body());
            throw new \RuntimeException('Error creating Mercado Pago preference: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get the status of a payment by its Mercado Pago payment ID.
     */
    public function getPayment(string $mpPaymentId): array
    {
        $response = Http::withToken($this->accessToken)
            ->get("{$this->baseUrl}/v1/payments/{$mpPaymentId}");

        if (!$response->successful()) {
            throw new \RuntimeException('Error fetching Mercado Pago payment: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Process an incoming webhook notification.
     * Returns the Mercado Pago payment data if it's a payment notification,
     * or null for other notification types.
     */
    public function processWebhook(array $payload): ?array
    {
        if (($payload['type'] ?? '') !== 'payment') {
            return null;
        }

        $mpId = $payload['data']['id'] ?? null;
        if (!$mpId) {
            return null;
        }

        return $this->getPayment((string) $mpId);
    }
}
