<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subscription $subscription,
        public int $daysLeft
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $planName = $this->subscription->plan?->getTranslation('name', 'ar') ?? 'الاشتراك';
        $endsAt   = $this->subscription->current_period_end?->format('Y/m/d')
            ?? $this->subscription->trial_ends_at?->format('Y/m/d')
            ?? '—';

        return (new MailMessage)
            ->subject("تنبيه: اشتراكك ينتهي خلال {$this->daysLeft} أيام")
            ->greeting('أهلاً ' . $notifiable->name . '،')
            ->line("اشتراكك في خطة **{$planName}** على منصة ميزان سينتهي خلال **{$this->daysLeft} أيام** بتاريخ {$endsAt}.")
            ->line('لضمان استمرارية عمل مكتبك بدون انقطاع، يُرجى تجديد الاشتراك قبل انتهاء المدة.')
            ->action('جدّد اشتراكك الآن', url('/admin/billing'))
            ->line('شكراً لثقتك في منصة ميزان.')
            ->salutation('فريق ميزان');
    }
}
