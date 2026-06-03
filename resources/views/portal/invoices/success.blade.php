<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex,nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم الدفع بنجاح — {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Tajawal', sans-serif; background: #f0f4f8; color: #1e293b; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem 1rem; }
        .card { background: #fff; border-radius: 20px; box-shadow: 0 4px 24px rgba(0,0,0,.08); padding: 3rem 2.5rem; max-width: 460px; width: 100%; text-align: center; }
        .icon-wrap { width: 80px; height: 80px; border-radius: 50%; background: #dcfce7; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
        .icon-wrap svg { width: 40px; height: 40px; color: #15803d; }
        h2 { font-size: 1.5rem; font-weight: 800; color: #1E3A5F; margin-bottom: .5rem; }
        p { color: #64748b; font-size: .95rem; margin-bottom: 1.5rem; }
        .invoice-box { background: #f8fafc; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.75rem; text-align: right; }
        .invoice-box .row { display: flex; justify-content: space-between; align-items: center; padding: .35rem 0; font-size: .88rem; }
        .invoice-box .row:not(:last-child) { border-bottom: 1px solid #f1f5f9; }
        .invoice-box .label { color: #94a3b8; }
        .invoice-box .value { font-weight: 600; color: #1e293b; }
        .invoice-box .value.amount { color: #15803d; font-size: 1.1rem; }
        .actions { display: flex; gap: .75rem; flex-wrap: wrap; justify-content: center; }
        .btn { padding: .65rem 1.5rem; border-radius: 10px; font-family: inherit; font-size: .9rem; font-weight: 700; text-decoration: none; transition: all .2s; }
        .btn-primary { background: #1E3A5F; color: #fff; }
        .btn-primary:hover { background: #2A4F80; }
        .btn-outline { background: #fff; color: #64748b; border: 1px solid #e2e8f0; }
        .btn-outline:hover { border-color: #1E3A5F; color: #1E3A5F; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon-wrap">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h2>تم الدفع بنجاح!</h2>
    <p>تم استلام دفعتك بنجاح وتحديث حالة الفاتورة.</p>

    <div class="invoice-box">
        <div class="row">
            <span class="label">رقم الفاتورة</span>
            <span class="value">{{ $invoice->invoice_number }}</span>
        </div>
        @if($invoice->legalCase)
        <div class="row">
            <span class="label">القضية</span>
            <span class="value">{{ $invoice->legalCase->case_number }}</span>
        </div>
        @endif
        <div class="row">
            <span class="label">المبلغ المدفوع</span>
            <span class="value amount">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</span>
        </div>
        <div class="row">
            <span class="label">تاريخ الدفع</span>
            <span class="value">{{ now()->format('Y/m/d H:i') }}</span>
        </div>
    </div>

    <div class="actions">
        <a href="{{ route('portal.invoices.show', $invoice->id) }}" class="btn btn-primary">عرض الفاتورة</a>
        <a href="{{ route('portal.invoices.index') }}" class="btn btn-outline">كل الفواتير</a>
    </div>
</div>
</body>
</html>
