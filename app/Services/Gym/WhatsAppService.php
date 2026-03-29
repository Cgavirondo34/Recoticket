<?php

namespace App\Services\Gym;

use App\Models\NotificationLog;
use App\Models\WhatsappTemplate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WhatsApp notification service.
 *
 * This is an abstraction layer so the underlying provider (e.g. Twilio,
 * WPPConnect, Evolution API) can be swapped without touching business logic.
 * Configure the provider in config/services.php under the "whatsapp" key.
 */
class WhatsAppService
{
    protected string $apiUrl;
    protected string $apiToken;
    protected string $from;

    public function __construct()
    {
        $this->apiUrl   = config('services.whatsapp.api_url', '');
        $this->apiToken = config('services.whatsapp.api_token', '');
        $this->from     = config('services.whatsapp.from', '');
    }

    /**
     * Send a WhatsApp message using a named template event.
     */
    public function sendFromTemplate(string $event, string $to, array $vars = [], ?int $tenantId = null): bool
    {
        $template = WhatsappTemplate::where('event', $event)
            ->where('active', true)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->first();

        if (!$template) {
            Log::warning("WhatsApp template not found for event: {$event}");
            return false;
        }

        $body = $template->render($vars);
        return $this->send($to, $body, $event, $tenantId);
    }

    /**
     * Send a raw WhatsApp message.
     */
    public function send(string $to, string $body, string $event = 'manual', ?int $tenantId = null): bool
    {
        $log = NotificationLog::create([
            'tenant_id' => $tenantId,
            'channel'   => 'whatsapp',
            'recipient' => $to,
            'event'     => $event,
            'body'      => $body,
            'status'    => 'pending',
        ]);

        if (empty($this->apiUrl)) {
            // No provider configured — log and skip
            $log->update(['status' => 'failed', 'error' => 'No WhatsApp provider configured.']);
            return false;
        }

        try {
            $response = Http::withToken($this->apiToken)
                ->post($this->apiUrl . '/send', [
                    'from'    => $this->from,
                    'to'      => $to,
                    'message' => $body,
                ]);

            if ($response->successful()) {
                $log->update(['status' => 'sent', 'sent_at' => now()]);
                return true;
            }

            $log->update(['status' => 'failed', 'error' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            $log->update(['status' => 'failed', 'error' => $e->getMessage()]);
            Log::error('WhatsApp send failed: ' . $e->getMessage());
            return false;
        }
    }
}
