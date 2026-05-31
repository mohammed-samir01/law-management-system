<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Services\Billing\PlatformBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SubscriptionBillingController extends Controller
{
    public function show()
    {
        $office = auth()->user()->office;
        abort_unless($office, 403);

        $subscription = $office->subscription;
        $plans        = Plan::where('is_active', true)->where('slug', '!=', 'trial')->orderBy('sort_order')->get();

        return view('billing.show', compact('office', 'subscription', 'plans'));
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'plan_id'       => ['required', 'exists:plans,id'],
            'billing_cycle' => ['required', Rule::in(['monthly', 'yearly'])],
        ]);

        $office = auth()->user()->office;
        abort_unless($office, 403);

        $plan  = Plan::findOrFail($validated['plan_id']);
        $cycle = $validated['billing_cycle'];
        $amount = PlatformBillingService::priceFor($plan, $cycle);

        // Ensure a subscription row exists for this office.
        $subscription = $office->subscription ?? Subscription::create([
            'office_id'     => $office->id,
            'plan_id'       => $plan->id,
            'status'        => 'expired',
            'billing_cycle' => $cycle,
        ]);

        try {
            $gateway = PlatformBillingService::gateway();

            $payment = SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'office_id'       => $office->id,
                'amount'          => $amount,
                'currency'        => $plan->currency,
                'billing_cycle'   => $cycle,
                'gateway'         => PlatformBillingService::gatewayName(),
                'status'          => 'pending',
            ]);

            $result = $gateway->charge([
                'amount'       => $amount,
                'currency'     => $plan->currency,
                'description'  => 'اشتراك ميزان — ' . $plan->getTranslation('name', 'ar') . " ({$cycle})",
                'callback_url' => route('billing.callback', ['payment' => $payment->id]),
                'first_name'   => auth()->user()->name,
                'email'        => auth()->user()->email,
                'phone'        => $office->phone ?? '',
            ]);

            if (! empty($result['data']['payment_url'])) {
                $payment->update(['reference' => $result['data']['order_id'] ?? null]);

                // Remember intended plan/cycle so callback/webhook can activate correctly.
                $subscription->update(['plan_id' => $plan->id, 'billing_cycle' => $cycle]);

                return redirect()->away($result['data']['payment_url']);
            }

            if ($result['success'] ?? false) {
                $this->activate($subscription, $payment, $result['data'] ?? []);

                return redirect()->route('billing.show')->with('success', 'تم تفعيل اشتراكك بنجاح.');
            }

            $payment->update(['status' => 'failed']);

            return redirect()->route('billing.show')->with('error', $result['message'] ?? 'فشل الدفع.');

        } catch (\Throwable $e) {
            Log::error('Subscription checkout failed', ['office_id' => $office->id, 'error' => $e->getMessage()]);

            return redirect()->route('billing.show')->with('error', $e->getMessage());
        }
    }

    public function callback(Request $request, SubscriptionPayment $payment)
    {
        // Confirm with the gateway, but treat webhook as the authoritative source.
        $paid = in_array($request->get('status'), ['paid', 'completed', 'success'])
            || $request->boolean('success')
            || $request->has('id');

        if ($paid && $payment->status !== 'completed') {
            $this->activate($payment->subscription, $payment, [
                'transaction_id' => $request->get('id') ?? $request->get('transaction_id'),
            ]);
        }

        $message = $payment->fresh()->status === 'completed'
            ? 'تم تفعيل اشتراكك بنجاح.'
            : 'لم يكتمل الدفع — يرجى المحاولة مرة أخرى.';

        return redirect()->route('billing.show')->with(
            $payment->fresh()->status === 'completed' ? 'success' : 'error',
            $message
        );
    }

    public function webhook(Request $request)
    {
        try {
            // Verify signature using the same gateway class.
            PlatformBillingService::gateway()->handleWebhook($request);
        } catch (\Throwable $e) {
            Log::warning('Platform billing webhook error', ['error' => $e->getMessage()]);
        }

        // Resolve the payment by reference/order id sent back by the gateway.
        $reference = $request->input('order') ?? $request->input('order_id') ?? $request->input('merchant_order_id');
        $txnId     = $request->input('id') ?? $request->input('transaction_id');

        $payment = SubscriptionPayment::where('reference', $reference)
            ->orWhere('gateway_transaction_id', $txnId)
            ->latest()
            ->first();

        $success = $request->boolean('success') || in_array($request->input('status'), ['paid', 'completed', 'success']);

        if ($payment && $success && $payment->status !== 'completed') {
            $this->activate($payment->subscription, $payment, ['transaction_id' => $txnId]);
        }

        return response()->json(['received' => true]);
    }

    private function activate(Subscription $subscription, SubscriptionPayment $payment, array $data): void
    {
        DB::transaction(function () use ($subscription, $payment, $data) {
            $payment->update([
                'status'                 => 'completed',
                'gateway_transaction_id' => $data['transaction_id'] ?? $payment->gateway_transaction_id,
                'paid_at'                => now(),
            ]);

            $end = $payment->billing_cycle === 'yearly'
                ? now()->addYear()
                : now()->addMonth();

            $subscription->update([
                'status'               => 'active',
                'billing_cycle'        => $payment->billing_cycle,
                'current_period_start' => now(),
                'current_period_end'   => $end,
            ]);
        });
    }
}
