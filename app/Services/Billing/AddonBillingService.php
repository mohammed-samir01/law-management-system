<?php

namespace App\Services\Billing;

use App\Models\Addon;
use App\Models\AddonPayment;
use App\Models\Office;
use App\Models\OfficeAddon;
use App\Notifications\AddonActivatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddonBillingService
{
    public static function checkout(Office $office, Addon $addon, string $cycle): array
    {
        $amount  = $addon->priceFor($cycle);
        $gateway = PlatformBillingService::gateway();

        $payment = AddonPayment::create([
            'office_id'     => $office->id,
            'addon_id'      => $addon->id,
            'amount'        => $amount,
            'currency'      => $addon->currency,
            'billing_cycle' => $cycle,
            'gateway'       => PlatformBillingService::gatewayName(),
            'status'        => 'pending',
        ]);

        $result = $gateway->charge([
            'amount'       => $amount,
            'currency'     => $addon->currency,
            'description'  => 'إضافة ميزان — ' . $addon->getTranslation('name', 'ar') . " ({$cycle})",
            'callback_url' => route('addons.callback', ['addonPayment' => $payment->id]),
            'first_name'   => $office->users()->first()?->name ?? $office->getTranslation('name', 'ar'),
            'email'        => $office->email ?? $office->users()->first()?->email ?? '',
            'phone'        => $office->phone ?? '',
        ]);

        if (! empty($result['data']['payment_url'])) {
            $payment->update(['reference' => $result['data']['order_id'] ?? null]);
        }

        return ['result' => $result, 'payment' => $payment];
    }

    public static function activate(AddonPayment $payment, array $data = []): void
    {
        DB::transaction(function () use ($payment, $data) {
            $payment->update([
                'status'                 => 'completed',
                'gateway_transaction_id' => $data['transaction_id'] ?? $payment->gateway_transaction_id,
                'paid_at'                => now(),
            ]);

            $end = $payment->billing_cycle === 'yearly'
                ? now()->addYear()
                : now()->addMonth();

            $officeAddon = OfficeAddon::updateOrCreate(
                ['office_id' => $payment->office_id, 'addon_id' => $payment->addon_id],
                [
                    'status'        => 'active',
                    'billing_cycle' => $payment->billing_cycle,
                    'activated_at'  => now(),
                    'expires_at'    => $end,
                    'cancelled_at'  => null,
                ]
            );

            $payment->update(['office_addon_id' => $officeAddon->id]);

            $admin = $payment->office?->users()
                ->whereHas('roles', fn ($q) => $q->where('name', 'office_admin'))
                ->first();

            $admin?->notify(new AddonActivatedNotification($payment->addon, $officeAddon));
        });
    }

    public static function handleWebhook(Request $request): void
    {
        $gateway = PlatformBillingService::gateway();

        try {
            $gateway->handleWebhook($request);
        } catch (\Throwable $e) {
            Log::warning('Addon webhook verification failed', ['error' => $e->getMessage()]);
            return;
        }

        $reference = $request->input('order')
            ?? $request->input('order_id')
            ?? $request->input('merchant_order_id');
        $txnId = $request->input('id') ?? $request->input('transaction_id');

        $payment = AddonPayment::when($reference, fn ($q) => $q->where('reference', $reference))
            ->when($txnId && !$reference, fn ($q) => $q->orWhere('gateway_transaction_id', $txnId))
            ->where('status', 'pending')
            ->first();

        $success = in_array($request->input('status'), ['paid', 'completed', 'success'])
            || $request->boolean('success')
            || $request->has('id');

        if ($payment && $success) {
            static::activate($payment, [
                'transaction_id' => $txnId,
            ]);
        }
    }
}
