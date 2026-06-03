<!DOCTYPE html>
<html lang="ar" dir="rtl" x-data x-bind:class="$store.theme.dark ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#1E3A5F">
    <title>@yield('title', 'ميزان')</title>

    @if(auth()->check() && auth()->user()->hasAnyRole(['lawyer', 'office_admin']) && auth()->user()->office?->hasAddon('lawyer-pwa'))
    {{-- Lawyer PWA (lawyer-pwa addon) --}}
    <link rel="manifest" href="{{ route('mobile.lawyer.pwa.manifest') }}">
    <link rel="apple-touch-icon" href="{{ route('mobile.lawyer.pwa.icon', ['size' => 192]) }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('{{ route('mobile.lawyer.pwa.sw') }}', { scope: '/mobile/' });
            });
        }
    </script>
    @endif
    {{-- Apply dark class before paint to prevent FOUC --}}
    <script>if(localStorage.getItem('amer_theme')==='dark')document.documentElement.classList.add('dark');</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Tajawal', sans-serif; overscroll-behavior: none; }
        .safe-top { padding-top: env(safe-area-inset-top); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
        .pb-nav { padding-bottom: calc(4.5rem + env(safe-area-inset-bottom)); }
    </style>
    @stack('styles')
</head>
<body
    class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen"
    x-data
    x-init="
        Alpine.store('theme', {
            dark: localStorage.getItem('amer_theme') === 'dark',
            toggle() {
                this.dark = !this.dark;
                localStorage.setItem('amer_theme', this.dark ? 'dark' : 'light');
                document.documentElement.classList.toggle('dark', this.dark);
            }
        });
        document.documentElement.classList.toggle('dark', Alpine.store('theme').dark);
    "
>

    @include('mobile.components.offline-banner')

    {{-- Header --}}
    @hasSection('header')
    <header class="bg-[#1E3A5F] text-white safe-top sticky top-0 z-40 shadow-md">
        <div class="flex items-center justify-between px-4 py-3">
            @yield('header-right')
            <h1 class="text-lg font-bold">@yield('header-title', 'ميزان')</h1>
            <div class="flex items-center gap-1">
                @yield('header-left')
                {{-- Dark mode toggle --}}
                <button @click="$store.theme.toggle()"
                        class="text-white/70 active:text-white p-1 rounded-lg"
                        title="تغيير المظهر">
                    <svg x-show="!$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>
    @endif

    {{-- Main Content --}}
    <main class="pb-nav min-h-screen @yield('main-class')">
        @yield('content')
    </main>

    {{-- Bottom Navigation --}}
    @hasSection('bottomnav')
    @yield('bottomnav')
    @endif

    @stack('scripts')

    {{-- Firebase Push Notifications --}}
    @auth
    @if(config('services.firebase.api_key'))
    <script type="module">
        import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js';
        import { getMessaging, getToken, onMessage } from 'https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging.js';

        const firebaseConfig = {
            apiKey:            '{{ config("services.firebase.api_key") }}',
            authDomain:        '{{ config("services.firebase.auth_domain") }}',
            projectId:         '{{ config("services.firebase.project_id") }}',
            storageBucket:     '{{ config("services.firebase.storage_bucket") }}',
            messagingSenderId: '{{ config("services.firebase.messaging_sender_id") }}',
            appId:             '{{ config("services.firebase.app_id") }}',
        };

        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        Notification.requestPermission().then(permission => {
            if (permission !== 'granted') return;

            getToken(messaging, { vapidKey: '{{ config("services.firebase.vapid_key") }}' })
                .then(token => {
                    if (!token) return;
                    fetch('{{ route("mobile.push.token") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ token, platform: 'android' }),
                    });
                })
                .catch(() => {});
        }).catch(() => {});
    </script>
    @endif
    @endauth
</body>
</html>
