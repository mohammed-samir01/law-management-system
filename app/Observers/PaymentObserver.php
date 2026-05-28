<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\NotificationService;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        if ($payment->status === 'completed') {
            NotificationService::onPaymentReceived($payment);
        }
    }

    public function updated(Payment $payment): void
    {
        if ($payment->isDirty('status') && $payment->status === 'completed') {
            $payment->loadMissing('client');
            NotificationService::onPaymentReceived($payment);
        }
    }
}
