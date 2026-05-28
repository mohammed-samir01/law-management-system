<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentUploadedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Document $document) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $title = $this->document->getTranslation('title', 'ar')
            ?: $this->document->getTranslation('title', 'en');

        return (new MailMessage)
            ->subject(__('notifications.document_uploaded'))
            ->greeting('أهلاً ' . $notifiable->name)
            ->line('تم رفع وثيقة جديدة: ' . $title)
            ->action('عرض الوثائق', url('/admin/documents'));
    }

    public function toArray(object $notifiable): array
    {
        $title = $this->document->getTranslation('title', 'ar')
            ?: $this->document->getTranslation('title', 'en');

        return [
            'type'        => 'document_uploaded',
            'title'       => __('notifications.document_uploaded'),
            'body'        => 'تم رفع وثيقة جديدة: ' . $title,
            'document_id' => $this->document->id,
            'url'         => '/admin/documents',
        ];
    }
}
