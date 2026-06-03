<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex,nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة {{ $invoice->invoice_number }} — {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Tajawal', sans-serif; background: #f0f4f8; color: #1e293b; min-height: 100vh; }
        header { background: #1E3A5F; color: #fff; padding: .75rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem; box-shadow: 0 2px 8px rgba(0,0,0,.2); }
        header .logo { display: flex; align-items: center; gap: .75rem; }
        header .logo-text { font-size: 1.1rem; font-weight: 700; }
        header .logo-sub { font-size: .75rem; color: #C9A84C; }
        header nav { display: flex; align-items: center; gap: 1rem; font-size: .85rem; flex-wrap: wrap; }
        header nav a { color: #93c5fd; text-decoration: none; }
        header nav a:hover { color: #C9A84C; }
        header nav form button { background: none; border: none; cursor: pointer; color: #fca5a5; font-family: inherit; font-size: .9rem; padding: 0; }

        .container { max-width: 760px; margin: 2rem auto; padding: 0 1rem; }
        .back-link { display: inline-flex; align-items: center; gap: .4rem; color: #64748b; text-decoration: none; font-size: .88rem; margin-bottom: 1.25rem; }
        .back-link:hover { color: #1E3A5F; }

        .card { background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.07); padding: 1.75rem; margin-bottom: 1.25rem; }
        .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9; }
        .card-title { font-size: 1.1rem; font-weight: 700; color: #1E3A5F; }

        .badge { display: inline-flex; align-items: center; padding: .25rem .8rem; border-radius: 999px; font-size: .8rem; font-weight: 600; }
        .badge-paid     { background: #dcfce7; color: #15803d; }
        .badge-sent     { background: #dbeafe; color: #1d4ed8; }
        .badge-overdue  { background: #fee2e2; color: #b91c1c; }
        .badge-draft    { background: #f1f5f9; color: #64748b; }
        .badge-cancelled{ background: #fef3c7; color: #92400e; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem 1.5rem; }
        .info-item label { font-size: .78rem; color: #94a3b8; display: block; margin-bottom: .2rem; }
        .info-item span { font-size: .95rem; font-weight: 600; color: #1e293b; }

        .amount-box { background: linear-gradient(135deg, #1E3A5F, #2A4F80); color: #fff; border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; margin-top: 1.25rem; }
        .amount-box .label { font-size: .88rem; opacity: .8; }
        .amount-box .value { font-size: 1.8rem; font-weight: 800; color: #C9A84C; }

        .section-title { font-size: .9rem; font-weight: 700; color: #1E3A5F; margin-bottom: 1rem; display: flex; align-items: center; gap: .5rem; }
        .section-title::before { content:''; width:4px; height:1rem; background:#C9A84C; border-radius:2px; display:inline-block; }

        .gateway-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: .75rem; }
        .gateway-option { position: relative; }
        .gateway-option input[type=radio] { position: absolute; opacity: 0; width: 0; height: 0; }
        .gateway-label {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer;
            transition: all .2s; text-align: center; background: #fff; gap: .4rem;
        }
        .gateway-label:hover { border-color: #C9A84C; background: #fffbf2; }
        .gateway-option input:checked + .gateway-label { border-color: #1E3A5F; background: #f0f5ff; }
        .gateway-icon { font-size: 1.75rem; }
        .gateway-name { font-size: .82rem; font-weight: 700; color: #1E3A5F; }
        .gateway-mode { font-size: .72rem; color: #94a3b8; }
        .gateway-test { color: #d97706; }

        .pay-btn {
            width: 100%; margin-top: 1.25rem; padding: .9rem;
            background: #C9A84C; color: #fff; border: none; border-radius: 12px;
            font-family: inherit; font-size: 1rem; font-weight: 700; cursor: pointer;
            transition: background .2s;
        }
        .pay-btn:hover { background: #b8963e; }
        .pay-btn:disabled { background: #94a3b8; cursor: not-allowed; }

        .history-table { width: 100%; border-collapse: collapse; font-size: .85rem; }
        .history-table th, .history-table td { padding: .6rem .8rem; text-align: right; }
        .history-table th { color: #94a3b8; font-weight: 600; border-bottom: 1px solid #f1f5f9; font-size: .78rem; }
        .history-table td { border-bottom: 1px solid #f8fafc; }

        @media(max-width:500px) { .info-grid { grid-template-columns: 1fr; } .gateway-grid { grid-template-columns: 1fr 1fr; } }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <div style="width:36px;height:36px;border-radius:50%;background:#C9A84C;display:flex;align-items:center;justify-content:center;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#1E3A5F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
                <line x1="12" y1="3" x2="12" y2="21"/><path d="M5 21h14"/><path d="M5 8l-3 5h6L5 8z"/><path d="M19 8l-3 5h6l-3-5z"/><line x1="5" y1="8" x2="19" y2="8"/>
            </svg>
        </div>
        <div>
            <div class="logo-text">عامر</div>
            <div class="logo-sub">بوابة العميل</div>
        </div>
    </div>
    <nav>
        <a href="{{ route('portal.dashboard') }}">لوحة التحكم</a>
        <a href="{{ route('portal.invoices.index') }}">فواتيري</a>
        <form method="POST" action="{{ route('portal.logout') }}">
            @csrf
            <button type="submit">تسجيل الخروج</button>
        </form>
    </nav>
</header>

<div class="container">
    <a href="{{ route('portal.invoices.index') }}" class="back-link">
        ← العودة للفواتير
    </a>

    {{-- Invoice details --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">فاتورة {{ $invoice->invoice_number }}</span>
            <span class="badge badge-{{ $invoice->status }}">{{ $invoice->status_label }}</span>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <label>رقم الفاتورة</label>
                <span>{{ $invoice->invoice_number }}</span>
            </div>
            <div class="info-item">
                <label>القضية</label>
                <span>{{ $invoice->legalCase?->case_number ?? '—' }}</span>
            </div>
            <div class="info-item">
                <label>تاريخ الإصدار</label>
                <span>{{ $invoice->created_at->format('Y/m/d') }}</span>
            </div>
            <div class="info-item">
                <label>تاريخ الاستحقاق</label>
                <span>{{ $invoice->due_date?->format('Y/m/d') ?? '—' }}</span>
            </div>
            <div class="info-item">
                <label>المبلغ الأساسي</label>
                <span>{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</span>
            </div>
            <div class="info-item">
                <label>الضريبة</label>
                <span>{{ number_format($invoice->tax_amount, 2) }} {{ $invoice->currency }}</span>
            </div>
        </div>

        <div class="amount-box">
            <span class="label">الإجمالي المستحق</span>
            <span class="value">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</span>
        </div>
    </div>

    {{-- Installment plan schedule (fee-installments addon) --}}
    @if($invoice->installmentPlan)
    <div class="card">
        <div class="section-title">{{ __('addons.inst_installments') }}</div>
        <table style="width:100%;border-collapse:collapse;font-size:.9rem;">
            <thead>
                <tr style="color:#64748b;text-align:start;">
                    <th style="padding:.5rem;text-align:start;">#</th>
                    <th style="padding:.5rem;text-align:start;">{{ __('addons.inst_amount') }}</th>
                    <th style="padding:.5rem;text-align:start;">{{ __('addons.inst_due_date') }}</th>
                    <th style="padding:.5rem;text-align:start;">{{ __('addons.inst_status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->installmentPlan->installments as $inst)
                <tr style="border-top:1px solid #1e293b;">
                    <td style="padding:.5rem;">{{ $inst->sequence }}</td>
                    <td style="padding:.5rem;">{{ number_format($inst->amount, 2) }} {{ $invoice->currency }}</td>
                    <td style="padding:.5rem;" dir="ltr">{{ $inst->due_date?->format('Y/m/d') }}</td>
                    <td style="padding:.5rem;">
                        <span style="color:{{ $inst->status === 'paid' ? '#16a34a' : ($inst->isOverdue() ? '#dc2626' : '#d97706') }};">
                            {{ __('addons.inst_status_' . $inst->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Payment form --}}
    @if(in_array($invoice->status, ['sent', 'overdue']))
        <div class="card">
            <div class="section-title">اختر طريقة الدفع</div>

            @if($gateways->isEmpty())
                <p style="color:#94a3b8;font-size:.9rem;text-align:center;padding:1.5rem 0;">
                    لا توجد بوابات دفع متاحة حالياً — يرجى التواصل مع المكتب.
                </p>
            @else
                <form method="POST" action="{{ route('portal.invoices.pay', $invoice->id) }}" id="payForm">
                    @csrf

                    @php
                    $icons = [
                        'paymob'        => '💳',
                        'instapay'      => '📱',
                        'vodafone_cash' => '📲',
                        'moyasar'       => '💳',
                        'mada'          => '🏧',
                        'paytabs'       => '💳',
                        'stripe'        => '💳',
                        'paypal'        => '🅿️',
                        'bank_transfer' => '🏦',
                    ];
                    @endphp

                    <div class="gateway-grid">
                        @foreach($gateways as $gw)
                        <div class="gateway-option">
                            <input type="radio" name="gateway" id="gw_{{ $gw->id }}"
                                   value="{{ $gw->gateway_name }}"
                                   {{ $loop->first ? 'checked' : '' }}>
                            <label class="gateway-label" for="gw_{{ $gw->id }}">
                                <span class="gateway-icon">{{ $icons[$gw->gateway_name] ?? '💰' }}</span>
                                <span class="gateway-name">{{ $gw->getTranslation('display_name', 'ar') ?: $gw->gateway_name }}</span>
                                @if($gw->test_mode)
                                    <span class="gateway-mode gateway-test">وضع الاختبار</span>
                                @endif
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <button type="submit" class="pay-btn" id="payBtn">
                        ادفع {{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}
                    </button>
                </form>

                <script>
                    document.getElementById('payForm').addEventListener('submit', function() {
                        document.getElementById('payBtn').disabled = true;
                        document.getElementById('payBtn').textContent = 'جاري المعالجة...';
                    });
                </script>
            @endif
        </div>
    @endif

    {{-- Payment history --}}
    @if($invoice->payments->isNotEmpty())
        <div class="card">
            <div class="section-title">سجل المدفوعات</div>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>البوابة</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $payment)
                    <tr>
                        <td>{{ $payment->created_at->format('Y/m/d H:i') }}</td>
                        <td>{{ $payment->gateway ?? $payment->method }}</td>
                        <td style="font-weight:600;color:#1E3A5F;">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                        <td>
                            @php
                            $sc = ['completed'=>'badge-paid','pending'=>'badge-sent','failed'=>'badge-overdue','refunded'=>'badge-draft'];
                            @endphp
                            <span class="badge {{ $sc[$payment->status] ?? '' }}">{{ $payment->status_label }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
</body>
</html>
