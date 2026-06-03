<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\AddonPayment;
use App\Services\Billing\AddonBillingService;
use App\Services\Billing\PlatformBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AddonBillingController extends Controller
{
    private const MARKETPLACE = 'filament.admin.pages.addons-marketplace-page';

    public function checkout(Request $request, Addon $addon)
    {
        $validated = $request->validate([
            'billing_cycle' => ['required', Rule::in(['monthly', 'yearly'])],
        ]);

        $office = Auth::user()?->office;
        abort_unless($office, 403);
        abort_unless($addon->is_active, 404);

        if ($office->hasAddon($addon->slug)) {
            return redirect()->back()->with('error', 'هذه الإضافة مفعّلة بالفعل على مكتبك.');
        }

        if (! PlatformBillingService::isConfigured()) {
            return redirect()->back()->with('error', 'بوابة الدفع غير مُعدَّة. تواصل مع الدعم الفني.');
        }

        try {
            $outcome = AddonBillingService::checkout($office, $addon, $validated['billing_cycle']);
            $result  = $outcome['result'];
            $payment = $outcome['payment'];

            if (! empty($result['data']['payment_url'])) {
                return redirect()->away($result['data']['payment_url']);
            }

            if ($result['success'] ?? false) {
                AddonBillingService::activate($payment, $result['data'] ?? []);
                return redirect()->route(self::MARKETPLACE)
                    ->with('success', "تم تفعيل إضافة {$addon->getTranslation('name', 'ar')} بنجاح!");
            }

            $payment->update(['status' => 'failed']);
            return redirect()->back()->with('error', $result['message'] ?? 'فشلت عملية الدفع.');

        } catch (\Throwable $e) {
            Log::error('Addon checkout failed', [
                'office_id' => $office->id,
                'addon_id'  => $addon->id,
                'error'     => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function callback(Request $request, AddonPayment $payment)
    {
        abort_unless($payment->office_id === Auth::user()?->office?->id, 403);

        if ($payment->status === 'completed') {
            return redirect()->route(self::MARKETPLACE)->with('success', 'الإضافة مفعّلة بالفعل.');
        }

        $paid = in_array($request->get('status'), ['paid', 'completed', 'success'])
            || $request->boolean('success')
            || $request->has('id');

        if ($paid) {
            try {
                AddonBillingService::activate($payment, [
                    'transaction_id' => $request->get('id') ?? $request->get('transaction_id'),
                ]);
                return redirect()->route(self::MARKETPLACE)->with('success', 'تم تفعيل الإضافة بنجاح! 🎉');
            } catch (\Throwable $e) {
                Log::error('Addon activation failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
                return redirect()->route(self::MARKETPLACE)->with('error', 'حدث خطأ أثناء التفعيل — تواصل مع الدعم.');
            }
        }

        $payment->update(['status' => 'failed']);
        return redirect()->route(self::MARKETPLACE)
            ->with('error', 'لم يتم تأكيد الدفع — حاول مرة أخرى أو تواصل مع الدعم الفني.');
    }

    public function webhook(Request $request)
    {
        try {
            AddonBillingService::handleWebhook($request);
        } catch (\Throwable $e) {
            Log::error('Addon webhook error', ['error' => $e->getMessage()]);
        }

        return response()->json(['ok' => true]);
    }
}
