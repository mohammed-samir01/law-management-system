<?php

namespace App\Notifications;

use App\Models\LegalCase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CaseStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public LegalCase $case,
        public string $oldStatus,
        public string $newStatus,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.case_status_changed'))
            ->greeting('أهلاً ' . $notifiable->name)
            ->line(__('notifications.case_status_body', [
                'case_number' => $this->case->case_number,
                'old'         => $this->oldStatus,
                'new'         => $this->newStatus,
            ]))
            ->action('عرض القضية', url('/admin/legal-cases/' . $this->case->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'case_status_changed',
            'title'       => __('notifications.case_status_changed'),
            'body'        => __('notifications.case_status_body', [
                'case_number' => $this->case->case_number,
                'old'         => $this->oldStatus,
                'new'         => $this->newStatus,
            ]),
            'case_id'     => $this->case->id,
            'case_number' => $this->case->case_number,
            'old_status'  => $this->oldStatus,
            'new_status'  => $this->newStatus,
            'url'         => '/admin/legal-cases/' . $this->case->id,
        ];
    }
}
