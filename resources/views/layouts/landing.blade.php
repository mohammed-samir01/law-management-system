<!DOCTYPE html>
<html
    x-data="landingApp()"
    :class="{ 'dark': darkMode }"
    :lang="lang"
    :dir="lang === 'ar' ? 'rtl' : 'ltr'"
    class="scroll-smooth"
>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name') . ' — عامر للمحاماة')</title>
    <meta name="description" content="@yield('description', $settings['seo']['meta_description'] ?? 'مكتب عامر للمحاماة — خدمات قانونية متكاملة بأعلى مستوى من الاحترافية')">
    @if(!empty($settings['seo']['meta_keywords']))
    <meta name="keywords" content="{{ $settings['seo']['meta_keywords'] }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Social Sharing -->
    @php
        $ogTitle = $settings['seo']['meta_title'] ?? config('app.name') . ' — عامر للمحاماة';
        $ogDesc  = $settings['seo']['meta_description'] ?? 'مكتب عامر للمحاماة — خدمات قانونية متكاملة بأعلى مستوى من الاحترافية';
        $ogImgPath = $settings['seo']['og_image_path'] ?? null;
        $ogImage = $ogImgPath
            ? asset('storage/' . $ogImgPath)
            : asset('images/hero-default.webp');
    @endphp
    <meta property="og:type"         content="website">
    <meta property="og:locale"       content="{{ app()->isLocale('ar') ? 'ar_EG' : 'en_US' }}">
    <meta property="og:url"          content="{{ url()->current() }}">
    <meta property="og:title"        content="{{ $ogTitle }}">
    <meta property="og:description"  content="{{ $ogDesc }}">
    <meta property="og:image"        content="{{ $ogImage }}">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDesc }}">
    <meta name="twitter:image"       content="{{ $ogImage }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Tajawal', sans-serif; }
        [x-cloak] { display: none !important; }
        @php
            $primaryColor = $settings['branding']['primary_color'] ?? '#1E3A5F';
            $accentColor  = $settings['branding']['accent_color']  ?? '#C9A84C';
        @endphp
        :root {
            --color-navy:       {{ $primaryColor }};
            --color-navy-light: color-mix(in srgb, {{ $primaryColor }} 80%, white);
            --color-navy-dark:  color-mix(in srgb, {{ $primaryColor }} 80%, black);
            --color-gold:       {{ $accentColor }};
            --color-gold-light: color-mix(in srgb, {{ $accentColor }} 80%, white);
            --color-gold-dark:  color-mix(in srgb, {{ $accentColor }} 80%, black);
        }
    </style>
</head>
<body class="bg-white dark:bg-gray-950 text-gray-800 dark:text-gray-100 antialiased transition-colors duration-300">

    @include('landing.partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('landing.partials.footer')

    <!-- Scroll to top button -->
    <button
        x-data="{ show: false }"
        x-init="window.addEventListener('scroll', () => { show = window.scrollY > 400 })"
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 left-6 z-50 w-11 h-11 rounded-full bg-gold hover:bg-gold-dark shadow-lg flex items-center justify-center transition-colors duration-200"
        aria-label="العودة للأعلى"
    >
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
        </svg>
    </button>

</body>
</html>
