<?php

namespace App\Services\Notification\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Stub WhatsApp provider — replace with a real provider (Twilio, Meta Cloud API, etc.)
 * when API credentials are available.
 */
class StubWhatsAppProvider implements WhatsAppProviderInterface
{
    public function send(string $phone, string $message): array
    {
        Log::info("[WhatsApp STUB] To: {$phone} | Message: {$message}");

        // In production, replace this with real provider HTTP call, e.g.:
        // $response = Http::post('https://api.provider.com/messages', [...]);

        return [
            'success' => true,
            'provider_message_id' => 'stub-' . uniqid(),
            'error' => null,
        ];
    }
}
