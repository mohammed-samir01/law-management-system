<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invoice $invoice) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.invoice_due'))
            ->greeting('أهلاً ' . $notifiable->name)
            ->line(__('notifications.invoice_due_body', [
                'invoice_number' => $this->invoice->invoice_number,
                'due_date'       => $this->invoice->due_date?->format('Y/m/d'),
            ]))
            ->action('عرض الفاتورة', url('/admin/invoices/' . $this->invoice->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'invoice_due',
            'title'          => __('notifications.invoice_due'),
            'body'           => __('notifications.invoice_due_body', [
                'invoice_number' => $this->invoice->invoice_number,
                'due_date'       => $this->invoice->due_date?->format('Y/m/d'),
            ]),
            'invoice_id'     => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'due_date'       => $this->invoice->due_date?->toDateString(),
            'url'            => '/admin/invoices/' . $this->invoice->id,
        ];
    }
}
