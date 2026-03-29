<?php

use App\Services\Gym\MembershipService;
use App\Services\Notification\NotificationService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─────────────────────────────────────────────────────────────────────────────
// Process membership expirations daily at midnight.
// Updates expired memberships and member statuses.
// ─────────────────────────────────────────────────────────────────────────────
Schedule::call(function () {
    $service = app(MembershipService::class);
    $count = $service->processExpirations();
    logger("[Scheduler] Processed {$count} expired memberships.");
})->dailyAt('00:05')->name('gym.process-expirations')->withoutOverlapping();

// ─────────────────────────────────────────────────────────────────────────────
// Send WhatsApp reminders for memberships expiring in 3 days.
// ─────────────────────────────────────────────────────────────────────────────
Schedule::call(function () {
    $membershipService = app(MembershipService::class);
    $notificationService = app(NotificationService::class);

    $expiring = $membershipService->getUpcomingExpirations(3);

    foreach ($expiring as $membership) {
        $member = $membership->member;
        if ($member && $member->whatsapp) {
            $notificationService->sendToMember($member, 'payment_due', [
                'member_name' => $member->full_name,
                'plan_name' => $membership->plan?->name ?? '',
                'expiry_date' => $membership->end_date->format('d/m/Y'),
            ]);
        }
    }

    logger("[Scheduler] Sent expiration reminders for {$expiring->count()} memberships.");
})->dailyAt('09:00')->name('gym.send-expiration-reminders')->withoutOverlapping();
