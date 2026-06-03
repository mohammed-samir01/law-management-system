<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionRenewedNotification extends Notification implements ShouldQueue
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
        $endsAt   = $this->subscription->current_period_end?->format('Y/m/d') ?? '—';

        return (new MailMessage)
            ->subject('تم تجديد اشتراكك في منصة ميزان ✓')
            ->greeting('أهلاً ' . $notifiable->name . '،')
            ->line("تم تجديد اشتراكك في خطة **{$planName}** بنجاح.")
            ->line("الاشتراك ساري حتى تاريخ: **{$endsAt}**")
            ->action('الدخول إلى لوحة التحكم', url('/admin'))
            ->line('شكراً لثقتك في منصة ميزان.')
            ->salutation('فريق ميزان');
    }
}
