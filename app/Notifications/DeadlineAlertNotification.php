<?php

namespace App\Notifications;

use App\Models\CaseDeadline;
use App\Notifications\Channels\NativePushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeadlineAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CaseDeadline $deadline, public int $daysLeft) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', NativePushChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $case = $this->deadline->legalCase;

        return (new MailMessage)
            ->subject(__('deadlines.alert_subject', ['days' => $this->daysLeft]))
            ->greeting('تنبيه هام')
            ->line(__('deadlines.alert_body', [
                'type'        => __('deadlines.type_' . $this->deadline->type),
                'case_number' => $case?->case_number ?? '—',
                'date'        => $this->deadline->due_date?->format('Y/m/d'),
                'days'        => $this->daysLeft,
            ]))
            ->action('عرض القضية', url('/admin/legal-cases/' . $this->deadline->case_id));
    }

    public function toArray(object $notifiable): array
    {
        $case = $this->deadline->legalCase;

        return [
            'type'        => 'deadline_alert',
            'title'       => __('deadlines.alert_subject', ['days' => $this->daysLeft]),
            'body'        => __('deadlines.alert_body', [
                'type'        => __('deadlines.type_' . $this->deadline->type),
                'case_number' => $case?->case_number ?? '—',
                'date'        => $this->deadline->due_date?->format('Y/m/d'),
                'days'        => $this->daysLeft,
            ]),
            'deadline_id' => $this->deadline->id,
            'case_id'     => $this->deadline->case_id,
            'url'         => '/admin/legal-cases/' . $this->deadline->case_id,
        ];
    }

    public function toPush(object $notifiable): array
    {
        $case = $this->deadline->legalCase;

        return [
            'title' => __('deadlines.alert_subject', ['days' => $this->daysLeft]),
            'body'  => ($case?->case_number ?? '') . ' — ' . $this->deadline->due_date?->format('Y/m/d'),
            'data'  => ['type' => 'deadline_alert', 'case_id' => (string) $this->deadline->case_id],
        ];
    }
}
