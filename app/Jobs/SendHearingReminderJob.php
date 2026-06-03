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

        // Client-facing reminder via SMS / WhatsApp — gated per office addon.
        $this->remindClient($hearing, $case);

        Log::info('HearingReminderJob: sent to ' . $notified->count() . ' users for hearing #' . $hearing->id);
    }

    /**
     * Send a single SMS/WhatsApp reminder to the case client, if the office
     * activated the corresponding addon and the client has a phone number.
     */
    private function remindClient(Hearing $hearing, $case): void
    {
        $client = $case->client;
        if (! $client) {
            return;
        }

        $office = \App\Models\Office::withoutGlobalScopes()->find($hearing->office_id);
        if (! $office) {
            return;
        }

        $replace = [
            'case'     => $case->case_number ?? '—',
            'date'     => $hearing->scheduled_at?->format('Y/m/d H:i') ?? '—',
            'location' => $hearing->location ?? '—',
            'app'      => config('app.name'),
        ];

        // Paid channels — gated per office addon, require a phone number.
        if (filled($client->phone)) {
            if ($office->hasAddon('sms')) {
                \App\Jobs\SendSmsJob::dispatch($client->phone, __('addons.sms_hearing_reminder', $replace));
            }

            if ($office->hasAddon('whatsapp')) {
                \App\Jobs\SendWhatsappJob::dispatch($client->phone, __('addons.wa_hearing_reminder', $replace));
            }
        }

        // Telegram — free, ungated; requires the client to have linked their account.
        if (filled($client->telegram_chat_id) && \App\Services\Messaging\MessagingService::isTelegramConfigured()) {
            \App\Jobs\SendTelegramJob::dispatch($client->telegram_chat_id, __('addons.wa_hearing_reminder', $replace));
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('HearingReminderJob failed for hearing #' . $this->hearing->id, [
            'error' => $e->getMessage(),
        ]);
    }
}
