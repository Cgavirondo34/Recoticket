<?php

namespace App\Jobs\Gym;

use App\Services\Gym\MemberService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Updates member statuses for any member whose membership has expired.
 * Dispatched daily via the scheduler.
 */
class CheckExpiredMemberships implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(MemberService $memberService): void
    {
        $count = $memberService->syncExpiredStatuses();
        \Illuminate\Support\Facades\Log::info("CheckExpiredMemberships: {$count} member(s) marked expired.");
    }
}
