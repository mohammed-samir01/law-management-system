<!DOCTYPE html>
<html lang="ar" dir="rtl"
      x-data
      x-bind:class="$store.theme.dark ? 'dark' : ''"
      class="{{ session('theme','light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ميزان — إدارة مكاتب المحاماة')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('amer_theme') === 'dark') document.documentElement.classList.add('dark');
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 h-screen flex overflow-hidden select-none"
      x-init="
        Alpine.store('theme', {
            dark: localStorage.getItem('amer_theme') === 'dark',
            toggle() {
                this.dark = !this.dark;
                localStorage.setItem('amer_theme', this.dark ? 'dark' : 'light');
            }
        });
      ">

    {{-- ═══════════════════════════════════════════════════════════ Sidebar --}}
    <aside class="w-60 shrink-0 bg-[#1E3A5F] flex flex-col h-full">

        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-[#C9A84C] rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-sm">ع</span>
                </div>
                <div>
                    <p class="text-white font-bold text-base leading-none">ميزان</p>
                    <p class="text-white/50 text-xs mt-0.5">إدارة مكاتب المحاماة</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @php
                $navItems = [
                    ['route' => 'desktop.dashboard', 'label' => 'لوحة التحكم',  'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'desktop.cases',     'label' => 'القضايا',       'icon' => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3'],
                    ['route' => 'desktop.calendar',  'label' => 'الجلسات',       'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['route' => 'desktop.clients',   'label' => 'الموكلون',      'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                    ['route' => 'desktop.documents', 'label' => 'المستندات',     'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['route' => 'desktop.invoices',  'label' => 'الفواتير',      'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                    ['route' => 'desktop.reports',   'label' => 'التقارير',      'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ];
            @endphp

            @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors
                      {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*')
                          ? 'bg-white/15 text-white font-semibold'
                          : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $item['icon'] }}"/>
                </svg>
                {{ $item['label'] }}
            </a>
            @endforeach
        </nav>

        {{-- User Info --}}
        <div class="px-4 py-4 border-t border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-[#C9A84C] rounded-full flex items-center justify-center shrink-0">
                    <span class="text-white font-bold text-xs">{{ mb_substr(auth()->user()->name ?? 'م', 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-medium truncate">{{ auth()->user()->name ?? 'المستخدم' }}</p>
                    <p class="text-white/50 text-xs truncate">{{ auth()->user()->email ?? '' }}</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- ════════════════════════════════════════════════════════ Main Content --}}
    <div class="flex-1 flex flex-col h-full overflow-hidden">

        {{-- Top Bar --}}
        <header class="h-14 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center px-6 gap-4 shrink-0">

            {{-- Page Title --}}
            <h1 class="font-bold text-[#1E3A5F] dark:text-white text-base flex-1">@yield('page-title', 'لوحة التحكم')</h1>

            {{-- Search --}}
            @hasSection('search')
            @yield('search')
            @endif

            {{-- Dark Mode Toggle --}}
            <button @click="$store.theme.toggle()"
                    class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <svg x-show="!$store.theme.dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg x-show="$store.theme.dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>
        </header>

        {{-- Scrollable Content Area --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>

@stack('scripts')
</body>
</html>
