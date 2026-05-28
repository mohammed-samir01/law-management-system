<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(int $id): View
    {
        $invoice = Invoice::with(['client', 'legalCase'])->findOrFail($id);

        return view('mobile.payment.show', compact('invoice'));
    }

    public function initiate(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'card_name'   => 'required|string|max:100',
            'card_number' => 'required|digits:16',
            'card_cvc'    => 'required|digits_between:3,4',
            'card_month'  => 'required|digits_between:1,2',
            'card_year'   => 'required|digits:4',
        ]);

        $invoice = Invoice::with(['client'])->findOrFail($id);

        try {
            $gateway = PaymentService::gateway('moyasar', auth()->user()->office_id);

            $result = $gateway->charge([
                'amount'       => $invoice->total_amount,
                'currency'     => 'SAR',
                'description'  => 'فاتورة رقم ' . $invoice->invoice_number,
                'callback_url' => route('mobile.invoices.pay.success', $id),
                'card_name'    => $request->card_name,
                'card_number'  => $request->card_number,
                'card_cvc'     => $request->card_cvc,
                'card_month'   => $request->card_month,
                'card_year'    => $request->card_year,
            ]);

            if (! empty($result['payment_url'])) {
                return redirect()->away($result['payment_url']);
            }

            if ($result['success'] ?? false) {
                $invoice->update(['status' => 'paid']);
                return redirect()->route('mobile.invoices.pay.success', $id);
            }

            return redirect()->route('mobile.invoices.pay.failed', $id)
                ->with('error', $result['message'] ?? 'فشل الدفع');

        } catch (\Throwable $e) {
            Log::error('Mobile payment failed', ['invoice_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('mobile.invoices.pay.failed', $id)
                ->with('error', 'حدث خطأ أثناء معالجة الدفع');
        }
    }

    public function success(Request $request, int $id): View
    {
        $invoice = Invoice::with(['client', 'legalCase'])->findOrFail($id);

        // Moyasar sends status in query string after redirect
        if ($request->get('status') === 'paid' || $request->has('id')) {
            $invoice->update(['status' => 'paid']);
            $invoice->refresh();
        }

        return view('mobile.payment.success', compact('invoice'));
    }

    public function failed(int $id): View
    {
        $invoice = Invoice::with(['client'])->findOrFail($id);
        $error   = session('error', 'فشل الدفع — يرجى المحاولة مجدداً');

        return view('mobile.payment.failed', compact('invoice', 'error'));
    }
}
