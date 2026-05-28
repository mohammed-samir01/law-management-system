<?php

namespace App\Jobs;

use App\Models\Hearing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Runs daily via scheduler. Finds all hearings in the next 24 hours
 * and dispatches a reminder job for each.
 */
class DispatchHearingRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $hoursAhead = 24) {}

    public function handle(): void
    {
        $hearings = Hearing::withoutGlobalScopes()
            ->where('status', 'scheduled')
            ->whereBetween('scheduled_at', [now(), now()->addHours($this->hoursAhead)])
            ->with('legalCase')
            ->get();

        foreach ($hearings as $hearing) {
            SendHearingReminderJob::dispatch($hearing);
        }

        Log::info("DispatchHearingRemindersJob: dispatched {$hearings->count()} reminders.");
    }

    public function failed(\Throwable $e): void
    {
        Log::error('DispatchHearingRemindersJob failed', ['error' => $e->getMessage()]);
    }
}
