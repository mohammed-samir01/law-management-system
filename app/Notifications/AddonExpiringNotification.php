<?php

namespace App\Notifications;

use App\Models\Addon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AddonExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Addon $addon,
        public int $daysLeft
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $this->addon->getTranslation('name', 'ar');

        return (new MailMessage)
            ->subject("تنبيه: إضافة {$name} ستنتهي خلال {$this->daysLeft} أيام")
            ->greeting('أهلاً ' . $notifiable->name . '،')
            ->line("إضافة **{$name}** على مكتبك ستنتهي خلال **{$this->daysLeft} أيام**.")
            ->line('لتجنب انقطاع الخدمة، يُرجى تجديد الاشتراك قبل انتهاء المدة.')
            ->action('تجديد الإضافة الآن', url('/admin/addons'))
            ->line('شكراً لثقتك في منصة ميزان.')
            ->salutation('فريق ميزان');
    }
}
