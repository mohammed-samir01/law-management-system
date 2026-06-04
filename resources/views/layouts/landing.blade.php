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
    @php
        $__officeName  = $settings['branding']['name_ar'] ?? $settings['hero']['heading_ar'] ?? config('app.name');
        $__faviconPath = $settings['branding']['favicon_path'] ?? null;
        $__faviconUrl  = $__faviconPath
            ? (str_starts_with($__faviconPath, 'http') ? $__faviconPath : asset('storage/' . $__faviconPath))
            : asset('favicon.ico');
    @endphp
    <title>@yield('title', $__officeName)</title>
    <link rel="icon" type="image/x-icon" href="{{ $__faviconUrl }}">
    <link rel="shortcut icon" href="{{ $__faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $__faviconUrl }}">
    <meta name="description" content="@yield('description', $settings['seo']['meta_description'] ?? $__officeName)">
    @if(!empty($settings['seo']['meta_keywords']))
    <meta name="keywords" content="{{ $settings['seo']['meta_keywords'] }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="ar" href="{{ url()->current() }}?lang=ar">
    <link rel="alternate" hreflang="en" href="{{ url()->current() }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

    <!-- Open Graph / Social Sharing -->
    @php
        $ogTitle = $settings['seo']['meta_title'] ?? $__officeName;
        $ogDesc  = $settings['seo']['meta_description'] ?? $__officeName;
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

    @if(!empty($settings['tracking']['search_console_token']))
    <meta name="google-site-verification" content="{{ $settings['tracking']['search_console_token'] }}">
    @endif

    {{-- Schema.org LocalBusiness (LegalService) structured data --}}
    @php
        $schemaName        = $settings['branding']['name_ar'] ?? $settings['hero']['heading_ar'] ?? config('app.name');
        $schemaDesc        = $settings['seo']['meta_description'] ?? $settings['hero']['subtitle_ar'] ?? '';
        $schemaPhone       = $settings['contact']['phone'] ?? '';
        $schemaEmail       = $settings['contact']['email'] ?? '';
        $schemaAddress     = $settings['contact']['address_ar'] ?? '';
        $schemaCity        = $settings['contact']['address_ar'] ?? '';
        $schemaLat         = $settings['contact']['latitude'] ?? null;
        $schemaLng         = $settings['contact']['longitude'] ?? null;
        $schemaHoursAr     = $settings['contact']['working_hours_ar'] ?? '';
        $schemaUrl         = request()->getSchemeAndHttpHost();
        $schemaLogo        = !empty($settings['branding']['logo_path'])
            ? asset('storage/' . $settings['branding']['logo_path'])
            : null;
        $sameAs = array_values(array_filter([
            $settings['contact']['facebook']  ?? null,
            $settings['contact']['twitter_x'] ?? null,
            $settings['contact']['instagram'] ?? null,
            $settings['contact']['linkedin']  ?? null,
            $settings['contact']['youtube']   ?? null,
        ]));
        $schema = array_filter([
            '@context'        => 'https://schema.org',
            '@type'           => ['LegalService', 'LocalBusiness'],
            'name'            => $schemaName,
            'description'     => $schemaDesc ?: null,
            'url'             => $schemaUrl,
            'logo'            => $schemaLogo,
            'image'           => $schemaLogo,
            'telephone'       => $schemaPhone ?: null,
            'email'           => $schemaEmail ?: null,
            'openingHours'    => $schemaHoursAr ?: null,
            'address'         => $schemaAddress ? [
                '@type'         => 'PostalAddress',
                'streetAddress' => $schemaAddress,
                'addressCountry'=> 'EG',
            ] : null,
            'geo'             => ($schemaLat && $schemaLng) ? [
                '@type'     => 'GeoCoordinates',
                'latitude'  => (float) $schemaLat,
                'longitude' => (float) $schemaLng,
            ] : null,
            'sameAs'          => $sameAs ?: null,
            'priceRange'      => '$$',
            'currenciesAccepted' => 'EGP,SAR',
            'areaServed'      => ['EG', 'SA'],
        ]);
    @endphp
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

    {{-- og:locale:alternate for bilingual content --}}
    <meta property="og:locale:alternate" content="{{ app()->isLocale('ar') ? 'en_US' : 'ar_EG' }}">

    <!-- Google Fonts — preload for LCP -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        window.officeName = {
            ar: '{{ addslashes($settings['branding']['name_ar'] ?? $officeSlug ?? '') }}',
            en: '{{ addslashes($settings['branding']['name_en'] ?? $officeSlug ?? '') }}'
        };
        window.officeSubtitle = {
            ar: '{{ addslashes($settings['hero']['subtitle_ar'] ?? '') }}',
            en: '{{ addslashes($settings['hero']['subtitle_en'] ?? '') }}'
        };
        window.officeFoundedYear = '{{ $settings['hero']['founded_year'] ?? '' }}';
    </script>

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

    @php
        $tracking         = $settings['tracking'] ?? [];
        $officeGtmId      = $tracking['gtm_id']          ?? null;
        $officeGa4Id      = $tracking['ga4_id']          ?? null;
        $officeMetaPixel  = $tracking['meta_pixel_id']   ?? null;
        $officeTiktokPixel= $tracking['tiktok_pixel_id'] ?? null;
        $officeSnapPixel  = $tracking['snapchat_pixel_id'] ?? null;
    @endphp

    {{-- Google Tag Manager — takes priority --}}
    @if($officeGtmId)
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $officeGtmId }}');</script>
    @else
        {{-- GA4 --}}
        @if($officeGa4Id)
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $officeGa4Id }}"></script>
            <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $officeGa4Id }}');</script>
        @endif
        {{-- Meta Pixel --}}
        @if($officeMetaPixel)
            <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{{ $officeMetaPixel }}');fbq('track','PageView');</script>
            <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $officeMetaPixel }}&ev=PageView&noscript=1"/></noscript>
        @endif
        {{-- TikTok Pixel --}}
        @if($officeTiktokPixel)
            <script>!function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};ttq.load('{{ $officeTiktokPixel }}');ttq.page();}(window,document,'ttq');</script>
        @endif
        {{-- Snapchat Pixel --}}
        @if($officeSnapPixel)
            <script type="text/javascript">(function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function(){a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};a.queue=[];var s='script';r=t.createElement(s);r.async=!0;r.src=n;var u=t.getElementsByTagName(s)[0];u.parentNode.insertBefore(r,u);})(window,document,'https://sc-static.net/scevent.min.js');snaptr('init','{{ $officeSnapPixel }}');snaptr('track','PAGE_VIEW');</script>
        @endif
    @endif
</head>
<body class="bg-white dark:bg-gray-950 text-gray-800 dark:text-gray-100 antialiased transition-colors duration-300">

    {{-- GTM noscript fallback --}}
    @if($officeGtmId)
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $officeGtmId }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

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
        class="fixed bottom-6 end-6 z-50 w-11 h-11 rounded-full bg-gold hover:bg-gold-dark shadow-lg flex items-center justify-center transition-colors duration-200"
        aria-label="العودة للأعلى"
    >
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
        </svg>
    </button>

    {{-- Cookie Consent Banner — shown only when tracking pixels are active --}}
    @php
        $hasTracking = $officeGtmId || $officeGa4Id || $officeMetaPixel || $officeTiktokPixel || $officeSnapPixel;
    @endphp
    @if($hasTracking)
    <div
        x-data="{
            show: !localStorage.getItem('cookie_consent'),
            accept() { localStorage.setItem('cookie_consent', '1'); this.show = false; },
            decline() { localStorage.setItem('cookie_consent', '0'); this.show = false; }
        }"
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-0 inset-x-0 z-50 p-4"
        role="dialog"
        aria-label="إشعار ملفات تعريف الارتباط"
    >
        <div class="max-w-4xl mx-auto bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-2xl px-6 py-4 flex flex-col sm:flex-row items-center gap-4">
            <div class="flex items-start gap-3 flex-1">
                <span class="text-2xl shrink-0">🍪</span>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    نستخدم ملفات تعريف الارتباط (Cookies) لتحسين تجربتك وتحليل زيارات الموقع.
                    باستمرارك في التصفح، توافق على سياسة الخصوصية لدينا.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button
                    @click="decline()"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg transition-colors"
                >رفض</button>
                <button
                    @click="accept()"
                    class="px-5 py-2 text-sm font-semibold text-white rounded-lg transition-colors"
                    style="background-color: var(--color-navy);"
                    onmouseover="this.style.opacity='0.9'"
                    onmouseout="this.style.opacity='1'"
                >موافق</button>
            </div>
        </div>
    </div>
    @endif

    {{-- WhatsApp Chat Button --}}
    @php
        $waNumber  = preg_replace('/\D/', '', $settings['contact']['whatsapp'] ?? '');
        $waMessage = rawurlencode('مرحباً، أود الاستفسار عن خدمات ' . ($settings['branding']['name_ar'] ?? ''));
    @endphp
    @if($waNumber)
    <a
        href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}"
        target="_blank"
        rel="noopener noreferrer"
        x-data="{ tooltip: false }"
        @mouseenter="tooltip = true"
        @mouseleave="tooltip = false"
        class="fixed bottom-20 end-6 z-50 w-14 h-14 rounded-full shadow-xl flex items-center justify-center transition-transform duration-200 hover:scale-110"
        style="background-color: #25D366;"
        aria-label="تواصل عبر واتساب"
    >
        {{-- Pulse ring --}}
        <span class="absolute inline-flex h-full w-full rounded-full opacity-40 animate-ping" style="background-color: #25D366;"></span>

        {{-- WhatsApp icon --}}
        <svg class="w-7 h-7 text-white relative z-10" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>

        {{-- Tooltip --}}
        <span
            x-show="tooltip"
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-x-2"
            x-transition:enter-end="opacity-100 translate-x-0"
            class="absolute end-16 bg-gray-900 text-white text-xs font-medium px-3 py-1.5 rounded-lg whitespace-nowrap shadow-lg"
        >تواصل عبر واتساب</span>
    </a>
    @endif

    <!-- Smooth scroll without hash URLs -->
    <script>
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href^="#"]');
            if (!link) return;

            const targetId = link.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }
        });
    </script>

</body>
</html>
