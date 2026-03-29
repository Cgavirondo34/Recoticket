<?php

use App\Jobs\Gym\CheckExpiredMemberships;
use App\Jobs\Gym\SendPaymentReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Gym scheduled tasks ───────────────────────────────────────────────────
// Run daily at 8:00 AM to mark expired memberships
Schedule::job(CheckExpiredMemberships::class)->dailyAt('08:00');

// Run daily at 9:00 AM to send WhatsApp payment reminders
Schedule::job(SendPaymentReminders::class)->dailyAt('09:00');
