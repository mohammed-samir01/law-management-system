<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.payment_received'))
            ->greeting('أهلاً ' . $notifiable->name)
            ->line(__('notifications.payment_received_body', [
                'amount'   => number_format($this->payment->amount, 2),
                'currency' => $this->payment->currency,
                'client'   => $this->payment->client?->getTranslation('name', 'ar') ?? '—',
            ]))
            ->action('عرض المدفوعات', url('/admin/payments'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'payment_received',
            'title'      => __('notifications.payment_received'),
            'body'       => __('notifications.payment_received_body', [
                'amount'   => number_format($this->payment->amount, 2),
                'currency' => $this->payment->currency,
                'client'   => $this->payment->client?->getTranslation('name', 'ar') ?? '—',
            ]),
            'payment_id' => $this->payment->id,
            'amount'     => $this->payment->amount,
            'currency'   => $this->payment->currency,
            'url'        => '/admin/payments',
        ];
    }
}
