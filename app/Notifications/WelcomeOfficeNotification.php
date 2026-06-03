<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeOfficeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $planName  = $this->subscription->plan?->getTranslation('name', 'ar') ?? 'التجريبية';
        $trialDays = $this->subscription->plan?->trial_days ?? 30;
        $endsAt    = $this->subscription->trial_ends_at?->format('Y/m/d') ?? '—';

        return (new MailMessage)
            ->subject('أهلاً بك في منصة ميزان 🎉')
            ->greeting('أهلاً ' . $notifiable->name . '،')
            ->line("يسعدنا انضمامك إلى منصة **ميزان** لإدارة مكاتب المحاماة.")
            ->line("لقد بدأت فترتك التجريبية المجانية لمدة **{$trialDays} يوماً** على خطة {$planName}.")
            ->line("الفترة التجريبية سارية حتى: **{$endsAt}**")
            ->action('ابدأ الاستخدام الآن', url('/admin'))
            ->line('إذا احتجت أي مساعدة في الإعداد، فريق الدعم جاهز لمساعدتك.')
            ->salutation('مع تحيات فريق ميزان');
    }
}
