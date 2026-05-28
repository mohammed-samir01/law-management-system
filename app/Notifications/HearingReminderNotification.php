<?php

namespace App\Notifications;

use App\Models\Hearing;
use App\Notifications\Channels\NativePushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HearingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Hearing $hearing) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', NativePushChannel::class];
    }

    public function toPush(object $notifiable): array
    {
        $case = $this->hearing->legalCase;

        return [
            'title' => 'تذكير بجلسة — ' . ($case?->case_number ?? 'عامر'),
            'body'  => 'جلسة القضية ' . ($case?->case_number ?? '') . ' — ' . $this->hearing->scheduled_at?->format('Y/m/d H:i'),
            'data'  => [
                'type'       => 'hearing_reminder',
                'hearing_id' => (string) $this->hearing->id,
                'case_id'    => (string) $this->hearing->case_id,
            ],
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $case = $this->hearing->legalCase;

        return (new MailMessage)
            ->subject(__('notifications.hearing_reminder'))
            ->greeting('أهلاً ' . $notifiable->name)
            ->line(__('notifications.hearing_reminder_body', [
                'case_number' => $case?->case_number ?? '—',
                'date'        => $this->hearing->scheduled_at?->format('Y/m/d H:i'),
                'location'    => $this->hearing->location ?? '—',
            ]))
            ->action('عرض القضية', url('/admin/legal-cases/' . $case?->id))
            ->line('شكراً لاستخدامك ' . config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'hearing_reminder',
            'title'       => __('notifications.hearing_reminder'),
            'body'        => __('notifications.hearing_reminder_body', [
                'case_number' => $this->hearing->legalCase?->case_number ?? '—',
                'date'        => $this->hearing->scheduled_at?->format('Y/m/d H:i'),
                'location'    => $this->hearing->location ?? '—',
            ]),
            'hearing_id'  => $this->hearing->id,
            'case_id'     => $this->hearing->case_id,
            'url'         => '/admin/legal-cases/' . $this->hearing->case_id,
        ];
    }
}
