<?php

namespace App\Notifications;

use App\Models\Hearing;
use App\Notifications\Channels\NativePushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AiHearingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Hearing $hearing,
        public string  $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', NativePushChannel::class];
    }

    public function toPush(object $notifiable): array
    {
        return [
            'title' => 'تذكير بجلسة — ' . ($this->hearing->legalCase?->case_number ?? 'ميزان'),
            'body'  => $this->message,
            'data'  => [
                'type'       => 'ai_hearing_reminder',
                'hearing_id' => (string) $this->hearing->id,
                'case_id'    => (string) $this->hearing->case_id,
            ],
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'ai_hearing_reminder',
            'title'      => 'تذكير بجلسة',
            'body'       => $this->message,
            'hearing_id' => $this->hearing->id,
            'case_id'    => $this->hearing->case_id,
        ];
    }
}
