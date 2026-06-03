<?php

namespace App\Notifications;

use App\Models\Addon;
use App\Models\OfficeAddon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AddonActivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Addon $addon,
        public OfficeAddon $officeAddon
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name    = $this->addon->getTranslation('name', 'ar');
        $expires = $this->officeAddon->expires_at?->format('Y/m/d') ?? '—';

        return (new MailMessage)
            ->subject("تم تفعيل إضافة {$name} ✓")
            ->greeting('أهلاً ' . $notifiable->name . '،')
            ->line("تم تفعيل إضافة **{$name}** على مكتبك بنجاح.")
            ->line("الإضافة سارية حتى: **{$expires}**")
            ->action('الذهاب إلى لوحة التحكم', url('/admin'))
            ->line('شكراً لثقتك في منصة ميزان.')
            ->salutation('فريق ميزان');
    }
}
