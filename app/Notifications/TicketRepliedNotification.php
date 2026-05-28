<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SupportTicket $ticket,
        public TicketReply $reply,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.ticket_replied') . ' #' . $this->ticket->id)
            ->greeting('أهلاً ' . $notifiable->name)
            ->line('تم الرد على تذكرتك: ' . $this->ticket->title)
            ->line('الرد: ' . \Illuminate\Support\Str::limit($this->reply->content, 200))
            ->action('عرض التذكرة', url('/admin/support-tickets/' . $this->ticket->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'      => 'ticket_replied',
            'title'     => __('notifications.ticket_replied'),
            'body'      => 'رد جديد على تذكرة: ' . $this->ticket->title,
            'ticket_id' => $this->ticket->id,
            'reply_id'  => $this->reply->id,
            'url'       => '/admin/support-tickets/' . $this->ticket->id,
        ];
    }
}
