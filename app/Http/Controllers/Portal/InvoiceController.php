<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $client = auth()->user()->client;

        $invoices = Invoice::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->get();

        return view('portal.invoices.index', compact('invoices'));
    }

    public function show(int $id): View
    {
        $client  = auth()->user()->client;
        $invoice = Invoice::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->with(['legalCase', 'payments'])
            ->findOrFail($id);

        $gateways = PaymentGateway::withoutGlobalScopes()
            ->where('office_id', $invoice->office_id)
            ->where('is_active', true)
            ->get();

        return view('portal.invoices.show', compact('invoice', 'gateways'));
    }

    public function pay(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'gateway' => ['required', 'string'],
        ]);

        $client  = auth()->user()->client;
        $invoice = Invoice::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->findOrFail($id);

        if ($invoice->status === 'paid') {
            return back()->with('info', 'هذه الفاتورة مدفوعة بالفعل.');
        }

        try {
            $gateway = PaymentService::gateway($request->gateway, $invoice->office_id);

            $result = $gateway->charge([
                'amount'       => $invoice->total_amount,
                'currency'     => $invoice->currency,
                'description'  => 'فاتورة رقم ' . $invoice->invoice_number,
                'callback_url' => route('portal.invoices.pay.success', $id),
                'first_name'   => $client->name,
                'email'        => auth()->user()->email,
                'phone'        => $client->phone ?? '',
            ]);

            // Gateway returns a redirect URL (Paymob, Moyasar, Stripe, etc.)
            if (! empty($result['data']['payment_url'])) {
                // Store pending payment record
                Payment::create([
                    'office_id'  => $invoice->office_id,
                    'client_id'  => $client->id,
                    'invoice_id' => $invoice->id,
                    'case_id'    => $invoice->case_id,
                    'amount'     => $invoice->total_amount,
                    'currency'   => $invoice->currency,
                    'method'     => $request->gateway,
                    'gateway'    => $request->gateway,
                    'status'     => 'pending',
                    'reference'  => $result['data']['order_id'] ?? null,
                ]);

                return redirect()->away($result['data']['payment_url']);
            }

            // Gateway completed immediately (e.g. bank transfer instruction)
            if ($result['success'] ?? false) {
                return $this->markPaid($invoice, $client->id, $request->gateway, $result['data'] ?? []);
            }

            return redirect()->route('portal.invoices.pay.failed', $id)
                ->with('error', $result['message'] ?? 'فشل الدفع');

        } catch (\Throwable $e) {
            Log::error('Portal payment failed', ['invoice_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('portal.invoices.pay.failed', $id)
                ->with('error', 'حدث خطأ أثناء معالجة الدفع');
        }
    }

    public function success(Request $request, int $id): View
    {
        $client  = auth()->user()->client;
        $invoice = Invoice::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->with(['legalCase', 'payments'])
            ->findOrFail($id);

        // Handle redirect callbacks (Moyasar, Paymob, etc.)
        if (in_array($request->get('status'), ['paid', 'completed']) || $request->has('id')) {
            $pendingPayment = $invoice->payments()->where('status', 'pending')->latest()->first();

            DB::transaction(function () use ($invoice, $pendingPayment, $request) {
                if ($pendingPayment) {
                    $pendingPayment->update([
                        'status'                 => 'completed',
                        'gateway_transaction_id' => $request->get('id') ?? $request->get('transaction_id'),
                        'paid_at'                => now(),
                    ]);
                }
                $invoice->update(['status' => 'paid']);
            });

            $invoice->refresh();
        }

        return view('portal.invoices.success', compact('invoice'));
    }

    public function failed(int $id): View
    {
        $client  = auth()->user()->client;
        $invoice = Invoice::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->findOrFail($id);

        $error = session('error', 'فشل الدفع — يرجى المحاولة مجدداً');

        // Mark pending payment as failed
        $invoice->payments()->where('status', 'pending')->update(['status' => 'failed']);

        return view('portal.invoices.failed', compact('invoice', 'error'));
    }

    private function markPaid(Invoice $invoice, int $clientId, string $gatewayName, array $data): RedirectResponse
    {
        DB::transaction(function () use ($invoice, $clientId, $gatewayName, $data) {
            Payment::create([
                'office_id'              => $invoice->office_id,
                'client_id'              => $clientId,
                'invoice_id'             => $invoice->id,
                'case_id'                => $invoice->case_id,
                'amount'                 => $invoice->total_amount,
                'currency'               => $invoice->currency,
                'method'                 => $gatewayName,
                'gateway'                => $gatewayName,
                'gateway_transaction_id' => $data['transaction_id'] ?? null,
                'status'                 => 'completed',
                'paid_at'                => now(),
            ]);
            $invoice->update(['status' => 'paid']);
        });

        return redirect()->route('portal.invoices.pay.success', $invoice->id);
    }
}
