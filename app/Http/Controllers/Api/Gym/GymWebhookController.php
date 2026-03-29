<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Models\WebhookEvent;
use App\Services\Gym\GymPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GymWebhookController extends Controller
{
    public function __construct(private readonly GymPaymentService $paymentService) {}

    /**
     * POST /api/gym/webhooks/mercado-pago
     * Receives and stores Mercado Pago webhook events, then processes them.
     * Idempotent: duplicate events are safely ignored.
     */
    public function mercadoPago(Request $request): JsonResponse
    {
        $payload = $request->all();
        $externalId = (string) ($payload['data']['id'] ?? '');
        $signature = $request->header('x-signature', '');

        // Deduplication: skip if already received
        $existing = WebhookEvent::where('source', 'mercado_pago')
            ->where('external_id', $externalId)
            ->first();

        if ($existing && $existing->isProcessed()) {
            return response()->json(['message' => 'already_processed'], 200);
        }

        $verified = $this->verifySignature($request);

        $event = $existing ?? WebhookEvent::create([
            'tenant_id'  => null, // resolved during processing
            'source'     => 'mercado_pago',
            'event_type' => $payload['type'] ?? null,
            'external_id'=> $externalId,
            'payload'    => $payload,
            'signature'  => $signature,
            'verified'   => $verified,
        ]);

        try {
            $this->paymentService->processMercadoPagoWebhook($event);
        } catch (\Throwable $e) {
            Log::error("GymWebhook processing failed: {$e->getMessage()}", ['event_id' => $event->id]);
            $event->update(['processing_error' => $e->getMessage()]);
        }

        return response()->json(['message' => 'ok'], 200);
    }

    /**
     * Verify the Mercado Pago webhook HMAC signature.
     * Returns false in non-production or if secret not configured.
     */
    private function verifySignature(Request $request): bool
    {
        $secret = config('services.mercado_pago.webhook_secret');
        if (empty($secret)) {
            return false;
        }

        $xSignature = $request->header('x-signature', '');
        $xRequestId = $request->header('x-request-id', '');
        $dataId = $request->input('data.id', '');

        $manifest = "id:{$dataId};request-id:{$xRequestId};ts:" . explode(',', $xSignature)[1] ?? '';
        $ts = explode('ts=', explode(',', $xSignature)[0] ?? '')[1] ?? '';
        $v1Hash = explode('v1=', $xSignature)[1] ?? '';

        $expectedHash = hash_hmac('sha256', "id:{$dataId};request-id:{$xRequestId};ts:{$ts}", $secret);

        return hash_equals($expectedHash, $v1Hash);
    }
}
