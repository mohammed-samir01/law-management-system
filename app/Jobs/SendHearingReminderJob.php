<?php

namespace App\Jobs;

use App\Models\Hearing;
use App\Notifications\HearingReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendHearingReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(public Hearing $hearing) {}

    public function handle(): void
    {
        $hearing = $this->hearing->loadMissing(['legalCase.lawyers', 'legalCase.client']);
        $case    = $hearing->legalCase;

        if (! $case) {
            Log::warning('HearingReminderJob: no case found for hearing #' . $hearing->id);
            return;
        }

        $notified = collect();

        // Notify all assigned lawyers
        foreach ($case->lawyers as $lawyer) {
            if (! $notified->contains($lawyer->id)) {
                $lawyer->notify(new HearingReminderNotification($hearing));
                $notified->push($lawyer->id);
            }
        }

        // Notify the case creator
        if ($case->createdBy && ! $notified->contains($case->createdBy->id)) {
            $case->createdBy->notify(new HearingReminderNotification($hearing));
            $notified->push($case->createdBy->id);
        }

        Log::info('HearingReminderJob: sent to ' . $notified->count() . ' users for hearing #' . $hearing->id);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('HearingReminderJob failed for hearing #' . $this->hearing->id, [
            'error' => $e->getMessage(),
        ]);
    }
}
