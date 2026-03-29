<?php

namespace App\Services\Notification;

use App\Models\Member;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Services\Notification\Providers\WhatsAppProviderInterface;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(
        private readonly WhatsAppProviderInterface $whatsAppProvider
    ) {}

    /**
     * Send a notification to a member using a template event key.
     * Logs the attempt and result.
     */
    public function sendToMember(Member $member, string $eventKey, array $variables = []): NotificationLog
    {
        $template = NotificationTemplate::where('tenant_id', $member->tenant_id)
            ->where('event_key', $eventKey)
            ->where('active', true)
            ->first();

        $renderedBody = $template
            ? $template->render($variables)
            : $this->fallbackMessage($eventKey, $variables);

        $log = NotificationLog::create([
            'tenant_id' => $member->tenant_id,
            'notification_template_id' => $template?->id,
            'event_key' => $eventKey,
            'channel' => 'whatsapp',
            'recipient_phone' => $member->whatsapp ?? $member->phone,
            'recipient_name' => $member->full_name,
            'member_id' => $member->id,
            'rendered_body' => $renderedBody,
            'status' => 'queued',
            'attempts' => 0,
        ]);

        return $this->dispatch($log);
    }

    /**
     * Send a raw WhatsApp message to an arbitrary phone number (non-member).
     */
    public function sendRaw(string $phone, string $message, ?int $tenantId = null): NotificationLog
    {
        $log = NotificationLog::create([
            'tenant_id' => $tenantId,
            'event_key' => 'raw',
            'channel' => 'whatsapp',
            'recipient_phone' => $phone,
            'rendered_body' => $message,
            'status' => 'queued',
            'attempts' => 0,
        ]);

        return $this->dispatch($log);
    }

    /**
     * Dispatch the notification via the provider and update the log.
     */
    private function dispatch(NotificationLog $log): NotificationLog
    {
        if (empty($log->recipient_phone)) {
            $log->update(['status' => 'failed', 'error_message' => 'No phone number available']);
            return $log;
        }

        $log->increment('attempts');

        $result = $this->whatsAppProvider->send($log->recipient_phone, $log->rendered_body);

        $log->update([
            'status' => $result['success'] ? 'sent' : 'failed',
            'provider_message_id' => $result['provider_message_id'],
            'sent_at' => $result['success'] ? now() : null,
            'error_message' => $result['error'],
        ]);

        if (! $result['success']) {
            Log::warning("[NotificationService] Failed to send {$log->event_key} to {$log->recipient_phone}: {$result['error']}");
        }

        return $log;
    }

    /**
     * Generate a basic fallback message when no template is configured.
     */
    private function fallbackMessage(string $eventKey, array $variables): string
    {
        $name = $variables['member_name'] ?? 'Member';
        return match ($eventKey) {
            'payment_due' => "Hola {$name}, tu membresía vence pronto. Por favor abona para continuar.",
            'payment_overdue' => "Hola {$name}, tu membresía está vencida. Por favor regulariza tu situación.",
            'payment_confirmed' => "Hola {$name}, tu pago fue confirmado. ¡Gracias!",
            'reservation_confirmed' => "Hola {$name}, tu reserva fue confirmada.",
            'reservation_reminder' => "Hola {$name}, recordatorio de tu reserva de hoy.",
            default => "Hola {$name}, tienes una notificación de {$eventKey}.",
        };
    }
}
