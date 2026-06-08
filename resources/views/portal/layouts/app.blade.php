<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex,nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('portal.client_portal')) — {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">

    @if(auth()->user()?->office?->hasAddon('client-pwa'))
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

        /* ── Header ── */
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
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .logo { display: flex; align-items: center; gap: .75rem; text-decoration: none; }
        .logo-icon {
            width: 36px; height: 36px; border-radius: 50%;
            background: #C9A84C;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .logo-text { font-size: 1.05rem; font-weight: 700; color: #fff; }
        .logo-sub  { font-size: .72rem; color: #C9A84C; }

        nav { display: flex; align-items: center; gap: .25rem; flex-wrap: wrap; }
        nav a {
            color: #93c5fd;
            text-decoration: none;
            font-size: .82rem;
            padding: .3rem .6rem;
            border-radius: 6px;
            transition: background .15s, color .15s;
            white-space: nowrap;
        }
        nav a:hover        { background: rgba(255,255,255,.1); color: #fff; }
        nav a.nav-active   { background: rgba(201,168,76,.2); color: #C9A84C; font-weight: 600; }

        .btn-lang {
            background: transparent;
            border: 1px solid #475569;
            color: #cbd5e1;
            border-radius: 6px;
            padding: .25rem .55rem;
            font-size: .78rem;
            cursor: pointer;
            font-family: inherit;
            transition: border-color .2s, color .2s;
        }
        .btn-lang:hover { border-color: #C9A84C; color: #C9A84C; }

        .btn-logout {
            background: none; border: none; cursor: pointer;
            color: #fca5a5; font-family: inherit; font-size: .82rem;
            padding: .3rem .6rem; border-radius: 6px;
            transition: background .15s;
        }
        .btn-logout:hover { background: rgba(239,68,68,.15); color: #f87171; }

        /* ── Hero ── */
        .hero {
            background: linear-gradient(135deg, #1E3A5F 0%, #2A4F80 100%);
            color: #fff;
            padding: 1.75rem 2rem 1.5rem;
        }
        .hero h2 { font-size: 1.5rem; margin-bottom: .25rem; }
        .hero p  { color: #93c5fd; font-size: .9rem; }

        /* ── Layout ── */
        .container { max-width: 1100px; margin: 1.5rem auto; padding: 0 1.25rem; }

        /* ── Cards ── */
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            border: 1px solid #f1f5f9;
            transition: box-shadow .2s, transform .2s;
        }
        .card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); transform: translateY(-2px); }
        .card-meta { display: flex; align-items: center; justify-content: space-between; margin-bottom: .5rem; }
        .card-number { font-size: .78rem; color: #94a3b8; font-weight: 500; }
        .card-title  { font-weight: 600; color: #1e293b; margin-bottom: .5rem; font-size: 1rem; line-height: 1.45; }
        .card-sub    { font-size: .82rem; color: #64748b; }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 260px), 1fr)); gap: 1rem; }

        /* ── Tables ── */
        .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: .85rem 1.1rem; text-align: right; font-size: .88rem; }
        th { background: #f8fafc; color: #64748b; font-weight: 600; border-bottom: 2px solid #e2e8f0; font-size: .8rem; }
        td { border-bottom: 1px solid #f8fafc; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }

        /* ── Badges ── */
        .badge {
            display: inline-flex; align-items: center;
            padding: .2rem .75rem; border-radius: 999px;
            font-size: .75rem; font-weight: 600; white-space: nowrap;
        }
        .badge-new        { background: #dbeafe; color: #1d4ed8; }
        .badge-active     { background: #dcfce7; color: #15803d; }
        .badge-pending    { background: #fef9c3; color: #a16207; }
        .badge-adjourned  { background: #fee2e2; color: #b91c1c; }
        .badge-closed     { background: #f1f5f9; color: #64748b; }
        .badge-archived   { background: #e0e7ff; color: #4338ca; }
        .badge-paid       { background: #dcfce7; color: #15803d; }
        .badge-sent       { background: #dbeafe; color: #1d4ed8; }
        .badge-overdue    { background: #fee2e2; color: #b91c1c; }
        .badge-draft      { background: #f1f5f9; color: #64748b; }
        .badge-cancelled  { background: #fef3c7; color: #92400e; }
        .badge-scheduled  { background: #dbeafe; color: #1d4ed8; }
        .badge-open       { background: #dcfce7; color: #15803d; }
        .badge-in_progress{ background: #fef9c3; color: #a16207; }
        .badge-resolved   { background: #dcfce7; color: #15803d; }
        .badge-reopened   { background: #fee2e2; color: #b91c1c; }
        .badge-urgent     { background: #fee2e2; color: #b91c1c; }
        .badge-normal     { background: #f1f5f9; color: #64748b; }
        .badge-low        { background: #f0fdf4; color: #166534; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .4rem 1rem; border-radius: 8px; font-size: .85rem;
            font-weight: 600; text-decoration: none; cursor: pointer;
            border: none; font-family: inherit; transition: all .2s;
        }
        .btn-primary  { background: #1E3A5F; color: #fff; }
        .btn-primary:hover { background: #2A4F80; }
        .btn-gold     { background: #C9A84C; color: #fff; }
        .btn-gold:hover { background: #b8963e; }
        .btn-outline  { background: #fff; color: #1E3A5F; border: 1px solid #1E3A5F; }
        .btn-outline:hover { background: #f0f4f8; }
        .btn-sm { padding: .28rem .7rem; font-size: .78rem; }

        /* ── Forms ── */
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; font-size: .85rem; font-weight: 600; color: #374151; margin-bottom: .4rem; }
        .form-control {
            width: 100%; padding: .55rem .85rem;
            border: 1px solid #d1d5db; border-radius: 8px;
            font-family: inherit; font-size: .9rem; color: #1e293b;
            transition: border-color .2s, box-shadow .2s;
            background: #fff;
        }
        .form-control:focus { outline: none; border-color: #1E3A5F; box-shadow: 0 0 0 3px rgba(30,58,95,.1); }

        /* ── Empty state ── */
        .empty {
            text-align: center; color: #94a3b8;
            padding: 3rem 1rem; background: #fff;
            border-radius: 12px; border: 1px dashed #e2e8f0;
        }
        .empty-icon { font-size: 2.5rem; margin-bottom: .5rem; }
        .empty p { font-size: .95rem; }

        /* ── Alerts ── */
        .alert { padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: .9rem; }
        .alert-success { background: #dcfce7; color: #15803d; }
        .alert-error   { background: #fee2e2; color: #b91c1c; }
        .alert-info    { background: #dbeafe; color: #1d4ed8; }

        /* ── Section headers ── */
        .section { margin-bottom: 2rem; }
        .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
        .section-title {
            font-size: 1.05rem; font-weight: 700; color: #1E3A5F;
            display: flex; align-items: center; gap: .5rem;
        }
        .section-title::before {
            content: ''; display: inline-block;
            width: 4px; height: 1.1rem;
            background: #C9A84C; border-radius: 2px;
        }

        /* ── Amount ── */
        .amount { font-weight: 700; color: #1E3A5F; }

        /* ── Chat / replies ── */
        .reply-bubble {
            padding: .85rem 1.1rem;
            border-radius: 10px;
            margin-bottom: .75rem;
            font-size: .9rem;
            line-height: 1.6;
        }
        .reply-client { background: #eff6ff; border-inline-start: 3px solid #1E3A5F; }
        .reply-office { background: #f8fafc; border-inline-start: 3px solid #C9A84C; }
        .reply-meta   { font-size: .75rem; color: #94a3b8; margin-bottom: .3rem; }

        @media (max-width: 640px) {
            .hero { padding: 1.25rem 1rem 1rem; }
            .hero h2 { font-size: 1.2rem; }
            th, td { padding: .6rem .75rem; font-size: .8rem; }
            nav a { font-size: .78rem; padding: .25rem .45rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

<header>
    <a class="logo" href="{{ route('portal.dashboard') }}">
        <div class="logo-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#1E3A5F" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
                <line x1="12" y1="3" x2="12" y2="21"/>
                <path d="M5 21h14"/>
                <path d="M5 8l-3 5h6L5 8z"/>
                <path d="M19 8l-3 5h6l-3-5z"/>
                <line x1="5" y1="8" x2="19" y2="8"/>
            </svg>
        </div>
        <div>
            <div class="logo-text">{{ config('app.name') }}</div>
            <div class="logo-sub">{{ __('portal.client_portal') }}</div>
        </div>
    </a>

    <nav>
        <a href="{{ route('portal.dashboard') }}"
           class="{{ request()->routeIs('portal.dashboard') ? 'nav-active' : '' }}">
            {{ __('portal.nav_dashboard') }}
        </a>
        <a href="{{ route('portal.cases.index') }}"
           class="{{ request()->routeIs('portal.cases.*') ? 'nav-active' : '' }}">
            {{ __('portal.my_cases') }}
        </a>
        <a href="{{ route('portal.hearings.index') }}"
           class="{{ request()->routeIs('portal.hearings.*') ? 'nav-active' : '' }}">
            {{ __('portal.upcoming_hearings') }}
        </a>
        <a href="{{ route('portal.documents.index') }}"
           class="{{ request()->routeIs('portal.documents.*') ? 'nav-active' : '' }}">
            {{ __('portal.my_documents') }}
        </a>
        <a href="{{ route('portal.invoices.index') }}"
           class="{{ request()->routeIs('portal.invoices.*') ? 'nav-active' : '' }}">
            {{ __('portal.my_invoices') }}
        </a>
        <a href="{{ route('portal.tickets.index') }}"
           class="{{ request()->routeIs('portal.tickets.*') ? 'nav-active' : '' }}">
            {{ __('portal.my_tickets') }}
        </a>

        <form method="POST" action="{{ route('portal.locale') }}" style="display:inline;">
            @csrf
            <input type="hidden" name="lang" value="{{ app()->isLocale('ar') ? 'en' : 'ar' }}">
            <button type="submit" class="btn-lang">
                {{ app()->isLocale('ar') ? 'English' : 'العربية' }}
            </button>
        </form>

        <form method="POST" action="{{ route('portal.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn-logout">{{ __('portal.logout') }}</button>
        </form>
    </nav>
</header>

<div class="hero">
    <h2>@yield('hero-title', __('portal.client_portal'))</h2>
    <p>@yield('hero-subtitle', now()->translatedFormat('l، d F Y'))</p>
</div>

<div class="container">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    @yield('content')
</div>

@stack('scripts')
</body>
</html>
