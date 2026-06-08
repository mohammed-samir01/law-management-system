@extends('portal.layouts.app')

@section('title', __('portal.invoice') . ' ' . $invoice->invoice_number)
@section('hero-title', __('portal.invoice') . ' ' . $invoice->invoice_number)
@section('hero-subtitle', __('portal.issued_on', ['date' => $invoice->created_at->format('Y/m/d')]))

@push('styles')
<style>
    .container { max-width: 780px; }
    .back-link { display:inline-flex;align-items:center;gap:.4rem;color:#64748b;text-decoration:none;font-size:.88rem;margin-bottom:1.25rem; }
    .back-link:hover { color:#1E3A5F; }
    .info-grid { display:grid;grid-template-columns:1fr 1fr;gap:.75rem 1.5rem; }
    .info-item label { font-size:.78rem;color:#94a3b8;display:block;margin-bottom:.2rem; }
    .info-item span  { font-size:.95rem;font-weight:600;color:#1e293b; }
    .amount-box { background:linear-gradient(135deg,#1E3A5F,#2A4F80);color:#fff;border-radius:12px;padding:1.25rem 1.5rem;display:flex;align-items:center;justify-content:space-between;margin-top:1.25rem; }
    .amount-box .lbl { font-size:.88rem;opacity:.8; }
    .amount-box .val { font-size:1.8rem;font-weight:800;color:#C9A84C; }
    .gateway-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:.75rem; }
    .gateway-option { position:relative; }
    .gateway-option input[type=radio] { position:absolute;opacity:0;width:0;height:0; }
    .gateway-label { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:1rem;border:2px solid #e2e8f0;border-radius:12px;cursor:pointer;transition:all .2s;text-align:center;background:#fff;gap:.4rem; }
    .gateway-label:hover { border-color:#C9A84C;background:#fffbf2; }
    .gateway-option input:checked + .gateway-label { border-color:#1E3A5F;background:#f0f5ff; }
    .gateway-icon { font-size:1.75rem; }
    .gateway-name { font-size:.82rem;font-weight:700;color:#1E3A5F; }
    .gateway-test { font-size:.72rem;color:#d97706; }
    .pay-btn { width:100%;margin-top:1.25rem;padding:.9rem;background:#C9A84C;color:#fff;border:none;border-radius:12px;font-family:inherit;font-size:1rem;font-weight:700;cursor:pointer;transition:background .2s; }
    .pay-btn:hover { background:#b8963e; }
    .pay-btn:disabled { background:#94a3b8;cursor:not-allowed; }
    @media(max-width:500px) { .info-grid { grid-template-columns:1fr; } .gateway-grid { grid-template-columns:1fr 1fr; } }
</style>
@endpush

@section('content')

<a href="{{ route('portal.invoices.index') }}" class="back-link">← {{ __('portal.back_to_invoices') }}</a>

{{-- Invoice details --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;padding-bottom:1rem;border-bottom:1px solid #f1f5f9;">
        <span style="font-size:1.1rem;font-weight:700;color:#1E3A5F;">{{ __('portal.invoice') }} {{ $invoice->invoice_number }}</span>
        <span class="badge badge-{{ $invoice->status }}">{{ $invoice->status_label }}</span>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <label>{{ __('portal.invoice_number') }}</label>
            <span>{{ $invoice->invoice_number }}</span>
        </div>
        <div class="info-item">
            <label>{{ __('portal.case_number') }}</label>
            <span>{{ $invoice->legalCase?->case_number ?? '—' }}</span>
        </div>
        <div class="info-item">
            <label>{{ __('portal.issue_date') }}</label>
            <span>{{ $invoice->created_at->format('Y/m/d') }}</span>
        </div>
        <div class="info-item">
            <label>{{ __('portal.due_date') }}</label>
            <span>{{ $invoice->due_date?->format('Y/m/d') ?? '—' }}</span>
        </div>
        <div class="info-item">
            <label>{{ __('portal.subtotal') }}</label>
            <span>{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</span>
        </div>
        <div class="info-item">
            <label>{{ __('portal.tax') }}</label>
            <span>{{ number_format($invoice->tax_amount ?? 0, 2) }} {{ $invoice->currency }}</span>
        </div>
    </div>

    <div class="amount-box">
        <span class="lbl">{{ __('portal.total_due') }}</span>
        <span class="val">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</span>
    </div>
</div>

{{-- Installment plan --}}
@if($invoice->installmentPlan)
<div class="card" style="margin-bottom:1.25rem;">
    <div class="section-title">{{ __('addons.inst_installments') }}</div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('addons.inst_amount') }}</th>
                    <th>{{ __('addons.inst_due_date') }}</th>
                    <th>{{ __('addons.inst_status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->installmentPlan->installments as $inst)
                <tr>
                    <td>{{ $inst->sequence }}</td>
                    <td>{{ number_format($inst->amount, 2) }} {{ $invoice->currency }}</td>
                    <td dir="ltr">{{ $inst->due_date?->format('Y/m/d') }}</td>
                    <td>
                        <span style="color:{{ $inst->status === 'paid' ? '#16a34a' : ($inst->isOverdue() ? '#dc2626' : '#d97706') }};">
                            {{ __('addons.inst_status_' . $inst->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Payment form --}}
@if(in_array($invoice->status, ['sent', 'overdue']))
<div class="card" style="margin-bottom:1.25rem;">
    <div class="section-title">{{ __('portal.choose_payment') }}</div>

    @if($gateways->isEmpty())
        <p style="color:#94a3b8;font-size:.9rem;text-align:center;padding:1.5rem 0;">
            {{ __('portal.no_gateways') }}
        </p>
    @else
        <form method="POST" action="{{ route('portal.invoices.pay', $invoice->id) }}" id="payForm">
            @csrf
            @php
            $icons = ['paymob'=>'💳','instapay'=>'📱','vodafone_cash'=>'📲','moyasar'=>'💳','mada'=>'🏧','paytabs'=>'💳','stripe'=>'💳','paypal'=>'🅿️','bank_transfer'=>'🏦'];
            @endphp
            <div class="gateway-grid">
                @foreach($gateways as $gw)
                <div class="gateway-option">
                    <input type="radio" name="gateway" id="gw_{{ $gw->id }}"
                           value="{{ $gw->gateway_name }}"
                           {{ $loop->first ? 'checked' : '' }}>
                    <label class="gateway-label" for="gw_{{ $gw->id }}">
                        <span class="gateway-icon">{{ $icons[$gw->gateway_name] ?? '💰' }}</span>
                        <span class="gateway-name">{{ $gw->getTranslation('display_name', app()->getLocale()) ?: $gw->gateway_name }}</span>
                        @if($gw->test_mode)
                            <span class="gateway-test">{{ __('portal.test_mode') }}</span>
                        @endif
                    </label>
                </div>
                @endforeach
            </div>

            <button type="submit" class="pay-btn" id="payBtn">
                {{ __('portal.pay_amount', ['amount' => number_format($invoice->total_amount, 2) . ' ' . $invoice->currency]) }}
            </button>
        </form>
    @endif
</div>
@endif

{{-- Payment history --}}
@if($invoice->payments->isNotEmpty())
<div class="card">
    <div class="section-title">{{ __('portal.payment_history') }}</div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>{{ __('portal.date') }}</th>
                    <th>{{ __('portal.gateway') }}</th>
                    <th>{{ __('portal.amount') }}</th>
                    <th>{{ __('portal.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                @php $sc = ['completed'=>'badge-paid','pending'=>'badge-sent','failed'=>'badge-overdue','refunded'=>'badge-draft']; @endphp
                <tr>
                    <td>{{ $payment->created_at->format('Y/m/d H:i') }}</td>
                    <td>{{ $payment->gateway ?? $payment->method }}</td>
                    <td class="amount">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                    <td><span class="badge {{ $sc[$payment->status] ?? '' }}">{{ $payment->status_label }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    document.getElementById('payForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('payBtn');
        btn.disabled = true;
        btn.textContent = '{{ __("portal.processing") }}';
    });
</script>
@endpush
