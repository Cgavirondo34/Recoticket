<?php

namespace App\Jobs\Gym;

use App\Services\Gym\MemberService;
use App\Services\Gym\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Sends WhatsApp reminders to members whose memberships are expiring soon
 * or are already overdue. Dispatched daily via the scheduler.
 */
class SendPaymentReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(MemberService $memberService, WhatsAppService $whatsApp): void
    {
        // Expiring in 3 days
        foreach ($memberService->getDueSoon(3) as $member) {
            if (!$member->whatsapp) continue;
            $whatsApp->sendFromTemplate('payment_due_soon', $member->whatsapp, [
                'nombre'  => $member->full_name,
                'fecha'   => $member->membership_expires_at->format('d/m/Y'),
                'plan'    => $member->currentPlan?->name ?? '',
            ], $member->tenant_id);
        }

        // Expiring today
        foreach ($memberService->getDueSoon(0) as $member) {
            if (!$member->whatsapp) continue;
            $whatsApp->sendFromTemplate('payment_due_today', $member->whatsapp, [
                'nombre' => $member->full_name,
                'plan'   => $member->currentPlan?->name ?? '',
            ], $member->tenant_id);
        }

        // Already expired
        foreach ($memberService->getOverdue() as $member) {
            if (!$member->whatsapp) continue;
            $whatsApp->sendFromTemplate('payment_overdue', $member->whatsapp, [
                'nombre'  => $member->full_name,
                'fecha'   => $member->membership_expires_at?->format('d/m/Y') ?? '',
            ], $member->tenant_id);
        }
    }
}
