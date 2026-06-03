<?php

namespace App\Notifications;

use App\Models\Addon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AddonExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Addon $addon) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $this->addon->getTranslation('name', 'ar');

        return (new MailMessage)
            ->subject("انتهت إضافة {$name}")
            ->greeting('أهلاً ' . $notifiable->name . '،')
            ->line("انتهت صلاحية إضافة **{$name}** على مكتبك وتم إيقافها.")
            ->line('يمكنك إعادة تفعيلها في أي وقت من متجر الإضافات.')
            ->action('إعادة تفعيل الإضافة', url('/admin/addons'))
            ->line('شكراً لثقتك في منصة ميزان.')
            ->salutation('فريق ميزان');
    }
}
