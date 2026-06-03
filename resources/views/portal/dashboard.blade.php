<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex,nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('portal.client_portal') }} — {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">

    @if(auth()->user()?->office?->hasAddon('client-pwa'))
    {{-- PWA (client-pwa addon) --}}
    <meta name="theme-color" content="#1E3A5F">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="{{ route('portal.pwa.manifest') }}">
    <link rel="apple-touch-icon" href="{{ route('portal.pwa.icon', ['size' => 192]) }}">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('{{ route('portal.pwa.sw') }}', { scope: '/portal/' });
            });
        }
    </script>
    @endif
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Tajawal', sans-serif; background: #f0f4f8; color: #1e293b; min-height: 100vh; }

        header {
            background: #1E3A5F;
            color: #fff;
            padding: .75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.2);
        }
        header .logo { display: flex; align-items: center; gap: .75rem; }
        header .logo-text { font-size: 1.1rem; font-weight: 700; }
        header .logo-sub { font-size: .75rem; color: #C9A84C; font-weight: 400; }
        header nav { display: flex; align-items: center; gap: 1rem; font-size: .85rem; flex-wrap: wrap; }
        header nav a { color: #93c5fd; text-decoration: none; transition: color .2s; }
        header nav a:hover { color: #C9A84C; }
        header nav form button {
            background: none; border: none; cursor: pointer;
            color: #fca5a5; font-family: inherit; font-size: .9rem;
            padding: 0; transition: color .2s;
        }
        header nav form button:hover { color: #f87171; }

        .hero-banner {
            background: linear-gradient(135deg, #1E3A5F 0%, #2A4F80 100%);
            color: #fff;
            padding: 2rem 2rem 1.5rem;
        }
        .hero-banner h2 { font-size: 1.6rem; margin-bottom: .3rem; }
        .hero-banner p { color: #93c5fd; font-size: .95rem; }

        .stats-bar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.25rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem 2rem;
        }
        .stat-item { display: flex; align-items: center; gap: .5rem; font-size: .9rem; color: #64748b; }
        .stat-item .num { font-size: 1.2rem; font-weight: 700; color: #1E3A5F; }

        .container { max-width: 1100px; margin: 1.5rem auto; padding: 0 1rem; }

        .section { margin-bottom: 2.5rem; }
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1E3A5F;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .section-title::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 1.2rem;
            background: #C9A84C;
            border-radius: 2px;
        }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 260px), 1fr)); gap: 1rem; }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 1.2rem 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            border: 1px solid #f1f5f9;
            transition: box-shadow .2s, transform .2s;
        }
        .card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); transform: translateY(-2px); }
        .card-meta { display: flex; align-items: center; justify-content: space-between; margin-bottom: .5rem; }
        .card .case-number { font-size: .78rem; color: #94a3b8; font-weight: 500; }
        .card .case-title { font-weight: 600; color: #1e293b; margin-bottom: .6rem; font-size: 1rem; line-height: 1.4; }
        .card .case-court { font-size: .82rem; color: #64748b; margin-top: .4rem; }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: .2rem .75rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-active    { background: #dcfce7; color: #15803d; }
        .badge-pending   { background: #fef9c3; color: #a16207; }
        .badge-adjourned { background: #fee2e2; color: #b91c1c; }
        .badge-closed    { background: #f1f5f9; color: #64748b; }
        .badge-archived  { background: #e0e7ff; color: #4338ca; }
        .badge-paid      { background: #dcfce7; color: #15803d; }
        .badge-sent      { background: #dbeafe; color: #1d4ed8; }
        .badge-overdue   { background: #fee2e2; color: #b91c1c; }
        .badge-draft     { background: #f1f5f9; color: #64748b; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            border: 1px solid #f1f5f9;
        }
        th, td { padding: .85rem 1.1rem; text-align: right; font-size: .9rem; }
        th { background: #f8fafc; color: #64748b; font-weight: 600; border-bottom: 2px solid #e2e8f0; font-size: .82rem; }
        td { border-bottom: 1px solid #f8fafc; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }

        .empty {
            text-align: center;
            color: #94a3b8;
            padding: 3rem 1rem;
            background: #fff;
            border-radius: 12px;
            border: 1px dashed #e2e8f0;
        }
        .empty-icon { font-size: 2.5rem; margin-bottom: .5rem; }
        .empty p { font-size: .95rem; }

        .amount { font-weight: 700; color: #1E3A5F; }

        .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 12px; }
        .table-wrapper table { border-radius: 0; }

        @media (max-width: 600px) {
            .hero-banner { padding: 1.25rem 1rem 1rem; }
            .hero-banner h2 { font-size: 1.25rem; }
            th, td { padding: .6rem .75rem; font-size: .82rem; }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <div>
            <div class="logo-text">{{ config('app.name') }}</div>
            <div class="logo-sub">{{ __('portal.client_portal') }}</div>
        </div>
    </div>
    <nav>
        <span style="color:#cbd5e1">{{ __('portal.welcome', ['name' => auth()->user()->name]) }}</span>
        <a href="{{ route('portal.invoices.index') }}" style="color:#93c5fd;text-decoration:none;font-size:.85rem;">{{ __('portal.my_invoices') }}</a>
        <form method="POST" action="{{ route('portal.locale') }}" style="display:inline;">
            @csrf
            <input type="hidden" name="lang" value="{{ app()->isLocale('ar') ? 'en' : 'ar' }}">
            <button type="submit" style="background:transparent;border:1px solid #475569;color:#cbd5e1;border-radius:6px;padding:.25rem .6rem;font-size:.8rem;cursor:pointer;">
                {{ app()->isLocale('ar') ? 'English' : 'العربية' }}
            </button>
        </form>
        <form method="POST" action="{{ route('portal.logout') }}">
            @csrf
            <button type="submit">{{ __('portal.logout') }}</button>
        </form>
    </nav>
</header>

<div class="hero-banner">
    <h2>{{ __('portal.welcome', ['name' => auth()->user()->name]) }}</h2>
    <p>{{ now()->translatedFormat('l، d F Y') }}</p>
</div>

<div class="stats-bar">
    <div class="stat-item">
        <span class="num">{{ $cases->count() }}</span>
        <span>{{ __('portal.my_cases') }}</span>
    </div>
    <div class="stat-item">
        <span class="num">{{ $upcomingHearings->count() }}</span>
        <span>{{ __('portal.upcoming_hearings') }}</span>
    </div>
    <div class="stat-item">
        <span class="num">{{ $invoices->count() }}</span>
        <span>{{ __('portal.my_invoices') }}</span>
    </div>
</div>

<div class="container">

    {{-- My Cases --}}
    <div class="section">
        <div class="section-header">
            <div class="section-title">{{ __('portal.my_cases') }}</div>
        </div>

        @if($cases->isEmpty())
            <div class="empty">
                <div class="empty-icon">⚖️</div>
                <p>{{ __('portal.no_cases') }}</p>
            </div>
        @else
            <div class="grid">
                @foreach($cases as $case)
                <div class="card">
                    <div class="card-meta">
                        <span class="case-number">{{ $case->case_number }}</span>
                        <span class="badge badge-{{ $case->status }}">{{ $case->status_label }}</span>
                    </div>
                    <div class="case-title">{{ $case->getTranslation('title', 'ar') ?: $case->getTranslation('title', 'en') }}</div>
                    <div class="case-court">🏛️ {{ $case->court }}</div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Upcoming Hearings --}}
    <div class="section">
        <div class="section-header">
            <div class="section-title">{{ __('portal.upcoming_hearings') }}</div>
        </div>

        @if($upcomingHearings->isEmpty())
            <div class="empty">
                <div class="empty-icon">📅</div>
                <p>{{ __('portal.no_hearings') }}</p>
            </div>
        @else
            <div class="table-wrapper"><table>
                <thead>
                    <tr>
                        <th>{{ __('portal.date') }}</th>
                        <th>{{ __('portal.case_number') }}</th>
                        <th>{{ __('portal.court') }}</th>
                        <th>{{ __('portal.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcomingHearings as $hearing)
                    <tr>
                        <td><strong>{{ $hearing->scheduled_at->format('Y/m/d') }}</strong><br><small style="color:#94a3b8">{{ $hearing->scheduled_at->format('H:i') }}</small></td>
                        <td>{{ $hearing->legalCase?->case_number ?? '—' }}</td>
                        <td>{{ $hearing->location ?? '—' }}</td>
                        <td><span class="badge badge-active">مجدولة</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table></div>
        @endif
    </div>

    {{-- Invoices --}}
    <div class="section">
        <div class="section-header">
            <div class="section-title">{{ __('portal.my_invoices') }}</div>
        </div>

        @if($invoices->isEmpty())
            <div class="empty">
                <div class="empty-icon">🧾</div>
                <p>{{ __('portal.no_invoices') }}</p>
            </div>
        @else
            <div class="table-wrapper"><table>
                <thead>
                    <tr>
                        <th>{{ __('portal.invoice_number') }}</th>
                        <th>{{ __('portal.amount') }}</th>
                        <th>{{ __('portal.due_date') }}</th>
                        <th>{{ __('portal.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr>
                        <td><strong>{{ $invoice->invoice_number }}</strong></td>
                        <td class="amount">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</td>
                        <td>{{ $invoice->due_date?->format('Y/m/d') ?? '—' }}</td>
                        <td><span class="badge badge-{{ $invoice->status }}">{{ __('invoices.statuses.' . $invoice->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table></div>
        @endif
    </div>

</div>
</body>
</html>
