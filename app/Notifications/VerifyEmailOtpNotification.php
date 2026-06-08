<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailOtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $code,
        public int $ttlMinutes = 15,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $brand = \App\Models\PlatformSetting::get('brand.name_ar', 'ميزان');

        return (new MailMessage)
            ->subject("رمز تفعيل حسابك في {$brand} — Your verification code")
            ->greeting('مرحباً / Hello')
            ->line('رمز التحقق الخاص بك هو / Your verification code is:')
            ->line("**{$this->code}**")
            ->line("ينتهي هذا الرمز خلال {$this->ttlMinutes} دقيقة / This code expires in {$this->ttlMinutes} minutes.")
            ->line('إذا لم تطلب هذا الرمز، تجاهل هذه الرسالة. / If you did not request this, ignore this email.');
    }
}
