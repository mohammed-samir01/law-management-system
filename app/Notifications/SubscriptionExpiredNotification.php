<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $planName = $this->subscription->plan?->getTranslation('name', 'ar') ?? 'الاشتراك';

        return (new MailMessage)
            ->subject('انتهى اشتراكك في منصة ميزان')
            ->greeting('أهلاً ' . $notifiable->name . '،')
            ->line("انتهت صلاحية اشتراكك في خطة **{$planName}** على منصة ميزان.")
            ->line('تم تعليق وصول مكتبك مؤقتاً حتى يتم تجديد الاشتراك.')
            ->action('جدّد اشتراكك الآن', url('/admin/billing'))
            ->line('إذا كنت بحاجة للمساعدة، لا تتردد في التواصل مع فريق الدعم.')
            ->salutation('فريق ميزان');
    }
}
