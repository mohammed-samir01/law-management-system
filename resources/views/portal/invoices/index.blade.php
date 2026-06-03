<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex,nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فواتيري — {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Tajawal', sans-serif; background: #f0f4f8; color: #1e293b; min-height: 100vh; }
        header { background: #1E3A5F; color: #fff; padding: .75rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem; box-shadow: 0 2px 8px rgba(0,0,0,.2); }
        header .logo { display: flex; align-items: center; gap: .75rem; }
        header .logo-text { font-size: 1.1rem; font-weight: 700; }
        header .logo-sub { font-size: .75rem; color: #C9A84C; }
        header nav { display: flex; align-items: center; gap: 1rem; font-size: .85rem; flex-wrap: wrap; }
        header nav a { color: #93c5fd; text-decoration: none; transition: color .2s; }
        header nav a:hover, header nav a.active { color: #C9A84C; }
        header nav form button { background: none; border: none; cursor: pointer; color: #fca5a5; font-family: inherit; font-size: .9rem; padding: 0; }

        .hero-banner { background: linear-gradient(135deg, #1E3A5F 0%, #2A4F80 100%); color: #fff; padding: 2rem; }
        .hero-banner h2 { font-size: 1.5rem; margin-bottom: .3rem; }
        .hero-banner p { color: #93c5fd; font-size: .9rem; }

        .container { max-width: 1000px; margin: 1.5rem auto; padding: 0 1rem; }

        .filters { display: flex; gap: .75rem; margin-bottom: 1.25rem; flex-wrap: wrap; }
        .filter-btn { padding: .35rem 1rem; border-radius: 999px; border: 1px solid #e2e8f0; background: #fff; font-family: inherit; font-size: .82rem; cursor: pointer; color: #64748b; transition: all .2s; }
        .filter-btn.active, .filter-btn:hover { background: #1E3A5F; color: #fff; border-color: #1E3A5F; }

        .table-wrapper { overflow-x: auto; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: .85rem 1.1rem; text-align: right; font-size: .88rem; }
        th { background: #f8fafc; color: #64748b; font-weight: 600; border-bottom: 2px solid #e2e8f0; font-size: .8rem; }
        td { border-bottom: 1px solid #f8fafc; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }

        .badge { display: inline-flex; align-items: center; padding: .2rem .7rem; border-radius: 999px; font-size: .75rem; font-weight: 600; }
        .badge-paid     { background: #dcfce7; color: #15803d; }
        .badge-sent     { background: #dbeafe; color: #1d4ed8; }
        .badge-overdue  { background: #fee2e2; color: #b91c1c; }
        .badge-draft    { background: #f1f5f9; color: #64748b; }
        .badge-cancelled{ background: #fef3c7; color: #92400e; }

        .amount { font-weight: 700; color: #1E3A5F; }
        .btn { display: inline-flex; align-items: center; gap: .4rem; padding: .35rem .9rem; border-radius: 8px; font-size: .82rem; font-weight: 600; text-decoration: none; transition: all .2s; }
        .btn-primary { background: #1E3A5F; color: #fff; }
        .btn-primary:hover { background: #2A4F80; }
        .btn-gold { background: #C9A84C; color: #fff; }
        .btn-gold:hover { background: #b8963e; }

        .empty { text-align: center; color: #94a3b8; padding: 3rem 1rem; background: #fff; border-radius: 12px; border: 1px dashed #e2e8f0; }
        .empty-icon { font-size: 2.5rem; margin-bottom: .5rem; }
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
            <div class="logo-text">ميزان</div>
            <div class="logo-sub">بوابة العميل</div>
        </div>
    </div>
    <nav>
        <a href="{{ route('portal.dashboard') }}">لوحة التحكم</a>
        <a href="{{ route('portal.invoices.index') }}" class="active">فواتيري</a>
        <form method="POST" action="{{ route('portal.logout') }}">
            @csrf
            <button type="submit">تسجيل الخروج</button>
        </form>
    </nav>
</header>

<div class="hero-banner">
    <h2>فواتيري</h2>
    <p>عرض وإدارة جميع فواتيرك ومدفوعاتك</p>
</div>

<div class="container">

    @if(session('info'))
        <div style="background:#dbeafe;color:#1d4ed8;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.9rem;">
            {{ session('info') }}
        </div>
    @endif

    @if($invoices->isEmpty())
        <div class="empty">
            <div class="empty-icon">🧾</div>
            <p>لا توجد فواتير حتى الآن</p>
        </div>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>القضية</th>
                        <th>المبلغ</th>
                        <th>تاريخ الاستحقاق</th>
                        <th>الحالة</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr>
                        <td style="font-weight:600;">{{ $invoice->invoice_number }}</td>
                        <td style="color:#64748b;">{{ $invoice->legalCase?->case_number ?? '—' }}</td>
                        <td class="amount">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</td>
                        <td style="color:#64748b;">{{ $invoice->due_date?->format('Y/m/d') ?? '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $invoice->status }}">{{ $invoice->status_label }}</span>
                        </td>
                        <td>
                            <a href="{{ route('portal.invoices.show', $invoice->id) }}" class="btn btn-primary">
                                عرض
                                @if(in_array($invoice->status, ['sent','overdue']))
                                    &nbsp;/ دفع
                                @endif
                            </a>
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
