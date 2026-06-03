<?php

namespace App\Jobs;

use App\Models\Addon;
use App\Models\CaseDeadline;
use App\Models\DeadlineAlertLog;
use App\Models\Office;
use App\Notifications\DeadlineAlertNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Runs daily. For offices with the legal-deadlines addon active, alerts staff
 * (and client channels when their addons are active) ahead of each open deadline,
 * and marks past-due open deadlines as lapsed. Idempotent via deadline_alert_logs.
 */
class DispatchDeadlineAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function handle(): void
    {
        $addon = Addon::where('slug', 'legal-deadlines')->first();
        if (! $addon) {
            return;
        }

        $officeIds = $addon->officeAddons()->where('status', 'active')->pluck('office_id');
        if ($officeIds->isEmpty()) {
            return;
        }

        // 1) Mark lapsed: open deadlines whose due_date is in the past.
        CaseDeadline::withoutGlobalScopes()
            ->whereIn('office_id', $officeIds)
            ->where('status', 'open')
            ->whereDate('due_date', '<', today())
            ->update(['status' => 'lapsed']);

        // 2) Alert on open deadlines whose remaining days match an alert offset.
        $deadlines = CaseDeadline::withoutGlobalScopes()
            ->whereIn('office_id', $officeIds)
            ->where('status', 'open')
            ->whereDate('due_date', '>=', today())
            ->with(['legalCase.lawyers', 'legalCase.client', 'legalCase.createdBy'])
            ->get();

        $sent = 0;
        foreach ($deadlines as $deadline) {
            $daysLeft = (int) today()->diffInDays($deadline->due_date, false);
            $offsets  = $deadline->alert_offsets ?: config('legal_deadlines.default_offsets', [30, 14, 7, 3, 1]);

            if (! in_array($daysLeft, array_map('intval', $offsets), true)) {
                continue;
            }

            // idempotency guard
            $already = DeadlineAlertLog::where('case_deadline_id', $deadline->id)
                ->where('offset_days', $daysLeft)
                ->where('channel', 'app')
                ->exists();
            if ($already) {
                continue;
            }

            $this->notify($deadline, $daysLeft);

            DeadlineAlertLog::create([
                'office_id'        => $deadline->office_id,
                'case_deadline_id' => $deadline->id,
                'offset_days'      => $daysLeft,
                'channel'          => 'app',
                'sent_at'          => now(),
            ]);
            $sent++;
        }

        Log::info("DispatchDeadlineAlertsJob: sent {$sent} deadline alerts.");
    }

    private function notify(CaseDeadline $deadline, int $daysLeft): void
    {
        $case = $deadline->legalCase;
        if (! $case) {
            return;
        }

        // Staff (lawyers + case creator) — in-app / email / push.
        $notified = collect();
        foreach ($case->lawyers as $lawyer) {
            if (! $notified->contains($lawyer->id)) {
                $lawyer->notify(new DeadlineAlertNotification($deadline, $daysLeft));
                $notified->push($lawyer->id);
            }
        }
        if ($case->createdBy && ! $notified->contains($case->createdBy->id)) {
            $case->createdBy->notify(new DeadlineAlertNotification($deadline, $daysLeft));
        }

        // Client channels — gated by their own addons, reusing the messaging jobs.
        $client = $case->client;
        $office = Office::withoutGlobalScopes()->find($deadline->office_id);
        if (! $client || ! $office) {
            return;
        }

        $text = __('deadlines.alert_sms', [
            'type'        => __('deadlines.type_' . $deadline->type),
            'case_number' => $case->case_number ?? '—',
            'date'        => $deadline->due_date?->format('Y/m/d'),
            'days'        => $daysLeft,
        ]);

        if (filled($client->phone) && $office->hasAddon('sms')) {
            SendSmsJob::dispatch($client->phone, $text);
        }
        if (filled($client->phone) && $office->hasAddon('whatsapp')) {
            SendWhatsappJob::dispatch($client->phone, $text);
        }
        if (filled($client->telegram_chat_id) && \App\Services\Messaging\MessagingService::isTelegramConfigured()) {
            SendTelegramJob::dispatch($client->telegram_chat_id, $text);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('DispatchDeadlineAlertsJob failed', ['error' => $e->getMessage()]);
    }
}
