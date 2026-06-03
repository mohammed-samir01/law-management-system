@extends('layouts.saas')

@section('content')
@php
    $hero     = $platform['hero'] ?? [];
    $stats    = $platform['stats'] ?? [];
    $features = $platform['features'] ?? [];
    $whyUs    = $platform['why_us'] ?? [];
    $contact  = $platform['contact'] ?? [];

    $featureIcons = [
        'scale'    => 'M12 3v18M5 8l-3 5h6L5 8zm14 0l-3 5h6l-3-5zM5 21h14',
        'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'users'    => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2a4 4 0 10-4-4 4 4 0 004 4z',
        'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'cash'     => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        'sparkles' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
    ];
    $whyIcons = [
        'shield'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'bolt'    => 'M13 10V3L4 14h7v7l9-11h-7z',
        'headset' => 'M18.364 5.636a9 9 0 010 12.728M5.636 18.364a9 9 0 010-12.728m12.728 0A9 9 0 0112 21a9 9 0 01-6.364-2.636M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'refresh' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        'star'    => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
        'heart'   => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    ];
    $mapSrc = $platform['contact']['map_embed_src'] ?? '';
@endphp

<!-- Hero -->
<section id="hero" class="relative bg-navy text-white overflow-hidden min-h-screen flex items-center">

    {{-- Background layers --}}
    <div class="absolute inset-0 opacity-[0.07]" aria-hidden="true">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="geo" x="0" y="0" width="80" height="80" patternUnits="userSpaceOnUse">
                    <polygon points="40,5 75,25 75,55 40,75 5,55 5,25" fill="none" stroke="#C9A84C" stroke-width="0.6"/>
                    <circle cx="40" cy="40" r="2" fill="#C9A84C" opacity="0.5"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#geo)"/>
        </svg>
    </div>
    <div class="absolute inset-0 bg-gradient-to-br from-navy-dark via-navy to-navy-light/60" aria-hidden="true"></div>

    {{-- Decorative blobs --}}
    <div class="absolute top-1/4 start-0 w-96 h-96 rounded-full bg-gold/5 blur-3xl" aria-hidden="true"></div>
    <div class="absolute bottom-0 end-0 w-80 h-80 rounded-full bg-gold/8 blur-3xl" aria-hidden="true"></div>

    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-0">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            {{-- Text content (right in RTL) --}}
            <div class="order-2 lg:order-1 text-center lg:text-start">

                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 bg-gold/15 border border-gold/30 rounded-full px-4 py-1.5 mb-8"
                     style="animation: fadeInDown .6s ease both;">
                    <span class="w-2 h-2 rounded-full bg-gold animate-pulse"></span>
                    <span class="text-gold text-sm font-medium" x-text="lang==='ar'?'🎉 جرّب مجاناً لمدة شهر كامل':'🎉 Try free for a full month'"></span>
                </div>

                {{-- Heading --}}
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6"
                    style="font-family:'Tajawal',sans-serif; animation: fadeInUp .7s ease .1s both;"
                    x-text="lang==='ar'?'{{ addslashes($hero['heading_ar'] ?? '') }}':'{{ addslashes($hero['heading_en'] ?? '') }}'">
                </h1>

                {{-- Subtitle --}}
                <p class="text-lg text-gray-300 leading-relaxed mb-8 max-w-xl"
                   style="animation: fadeInUp .7s ease .2s both;"
                   x-text="lang==='ar'?'{{ addslashes($hero['subtitle_ar'] ?? '') }}':'{{ addslashes($hero['subtitle_en'] ?? '') }}'">
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-wrap justify-center lg:justify-start gap-4 mb-8"
                     style="animation: fadeInUp .7s ease .3s both;">
                    <a href="{{ route('register.plans') }}"
                       class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-gold hover:bg-gold-dark text-white font-bold text-base transition-all duration-300 shadow-lg shadow-gold/25 hover:-translate-y-0.5 hover:shadow-gold/40">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span x-text="lang==='ar'?'ابدأ مجاناً — شهر كامل':'Start Free — Full Month'"></span>
                    </a>
                    <a href="{{ route('pricing') }}"
                       class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl border border-white/25 hover:border-gold hover:text-gold text-white font-medium text-base transition-all duration-300 hover:-translate-y-0.5">
                        <span x-text="lang==='ar'?'عرض الأسعار':'View Pricing'"></span>
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>

                {{-- Trust line --}}
                <p class="text-sm text-gray-400" style="animation: fadeInUp .7s ease .4s both;"
                   x-text="lang==='ar'?'✓ بدون بطاقة دفع  ✓ إلغاء في أي وقت  ✓ كل المميزات':'✓ No credit card  ✓ Cancel anytime  ✓ All features'">
                </p>
            </div>

            {{-- Visual card (left in RTL) --}}
            <div class="order-1 lg:order-2 flex justify-center items-center"
                 style="animation: fadeInLeft .8s ease .2s both;">
                <div class="relative w-full max-w-md">

                    {{-- Main card --}}
                    <div class="relative bg-navy-light/40 backdrop-blur-sm border border-white/10 rounded-3xl p-6 shadow-2xl">

                        {{-- Card header --}}
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gold/20 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5 5 0 006 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5 5 0 006 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-sm" x-text="lang==='ar'?'لوحة التحكم':'Dashboard'"></div>
                                    <div class="text-gray-400 text-xs" x-text="lang==='ar'?'مكتب ميزان للمحاماة':'Mizan Law Office'"></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-2.5 h-2.5 rounded-full bg-green-400 animate-pulse"></div>
                                <span class="text-green-400 text-xs" x-text="lang==='ar'?'نشط':'Active'"></span>
                            </div>
                        </div>

                        {{-- Stats row --}}
                        <div class="grid grid-cols-3 gap-2 sm:gap-3 mb-5">
                            @foreach([['icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','val_ar'=>'١٢ قضية','val_en'=>'12 Cases','color'=>'blue'],['icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','val_ar'=>'٤ جلسات','val_en'=>'4 Hearings','color'=>'gold'],['icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','val_ar'=>'٨٥٠٠ ج.م','val_en'=>'8,500 EGP','color'=>'green']] as $s)
                            <div class="bg-white/5 rounded-xl p-3 text-center">
                                <svg class="w-5 h-5 mx-auto mb-1 {{ $s['color']==='gold'?'text-gold':($s['color']==='green'?'text-green-400':'text-blue-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $s['icon'] }}"/></svg>
                                <div class="text-white text-xs font-bold" x-text="lang==='ar'?'{{ $s['val_ar'] }}':'{{ $s['val_en'] }}'"></div>
                            </div>
                            @endforeach
                        </div>

                        {{-- AI feature highlight --}}
                        <div class="bg-gold/10 border border-gold/20 rounded-xl p-3.5 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gold/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-gold text-xs font-bold mb-0.5" x-text="lang==='ar'?'الذكاء الاصطناعي':'AI Assistant'"></div>
                                <div class="text-gray-300 text-xs truncate" x-text="lang==='ar'?'تلخيص وثيقة قانونية...':'Summarizing legal document...'"></div>
                            </div>
                            <div class="flex gap-1">
                                <div class="w-1.5 h-1.5 rounded-full bg-gold animate-bounce" style="animation-delay:0s"></div>
                                <div class="w-1.5 h-1.5 rounded-full bg-gold animate-bounce" style="animation-delay:.15s"></div>
                                <div class="w-1.5 h-1.5 rounded-full bg-gold animate-bounce" style="animation-delay:.3s"></div>
                            </div>
                        </div>

                        {{-- Progress bar --}}
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-gray-400 mb-1.5">
                                <span x-text="lang==='ar'?'استهلاك الذكاء الاصطناعي':'AI Usage'"></span>
                                <span>14/50</span>
                            </div>
                            <div class="h-1.5 bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-gold to-gold-light rounded-full" style="width:28%; transition: width 1.5s ease;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Floating badge --}}
                    <div class="absolute -top-4 -end-4 bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg shadow-green-500/30 animate-bounce" style="animation-duration:2s;">
                        <span x-text="lang==='ar'?'✓ نشط':'✓ Live'"></span>
                    </div>

                    {{-- Floating notification --}}
                    <div class="absolute -bottom-4 -start-4 bg-white dark:bg-gray-800 rounded-xl shadow-xl px-3 py-2 flex items-center gap-2 border border-gray-100 dark:border-gray-700"
                         style="animation: float 4s ease-in-out infinite;">
                        <div class="w-7 h-7 rounded-lg bg-blue-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <div class="text-navy dark:text-white text-xs font-bold" x-text="lang==='ar'?'جلسة جديدة':'New Hearing'"></div>
                            <div class="text-gray-400 text-xs" x-text="lang==='ar'?'غداً — ٩ صباحاً':'Tomorrow — 9 AM'"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 inset-x-0 flex justify-center animate-bounce">
        <a href="#features" class="text-white/30 hover:text-gold transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </a>
    </div>

    <style>
        @keyframes fadeInDown { from { opacity:0; transform:translateY(-20px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeInUp   { from { opacity:0; transform:translateY(20px);  } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeInLeft { from { opacity:0; transform:translateX(30px);  } to { opacity:1; transform:translateX(0); } }
        @keyframes float      { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-8px); } }
    </style>
</section>

@if($homepageVis['show_live_bar'] ?? true)
<!-- Live Trust Bar -->
<div class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-6 sm:gap-10 text-sm text-gray-500 dark:text-gray-400">

            {{-- Active offices --}}
            <div class="flex items-center gap-2">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                </span>
                <span>
                    <span class="font-bold text-navy dark:text-white" dir="ltr">{{ number_format($liveStats['offices']) }}+</span>
                    <span x-text="lang==='ar' ? ' مكتب محاماة نشط' : ' active law offices'"></span>
                </span>
            </div>

            <div class="hidden sm:block w-px h-5 bg-gray-200 dark:bg-gray-700"></div>

            {{-- Cases managed --}}
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span>
                    <span class="font-bold text-navy dark:text-white" dir="ltr">{{ number_format($liveStats['cases']) }}+</span>
                    <span x-text="lang==='ar' ? ' قضية مُدارة' : ' cases managed'"></span>
                </span>
            </div>

            <div class="hidden sm:block w-px h-5 bg-gray-200 dark:bg-gray-700"></div>

            {{-- Trial --}}
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>
                    <span class="font-bold text-navy dark:text-white" dir="ltr">{{ number_format($liveStats['trial_offices']) }}</span>
                    <span x-text="lang==='ar' ? ' مكتب في التجربة المجانية الآن' : ' offices on free trial now'"></span>
                </span>
            </div>

            <div class="hidden sm:block w-px h-5 bg-gray-200 dark:bg-gray-700"></div>

            {{-- No card --}}
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span x-text="lang==='ar' ? 'بدون بطاقة دفع' : 'No credit card required'"></span>
            </div>

        </div>
    </div>
</div>

@endif

<!-- Stats -->
<section class="bg-navy-dark dark:bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-6 md:gap-8 text-center">
        @foreach($stats as $stat)
        <div>
            <div class="text-4xl font-extrabold text-gold" dir="ltr" style="unicode-bidi:isolate;"><span data-counter="{{ (int) ($stat['value'] ?? 0) }}">0</span>{{ $stat['suffix'] ?? '' }}</div>
            <div class="text-sm text-gray-300 mt-2" x-text="lang==='ar'?'{{ addslashes($stat['label_ar'] ?? '') }}':'{{ addslashes($stat['label_en'] ?? '') }}'"></div>
        </div>
        @endforeach
    </div>
</section>

<!-- Features -->
<section id="features" class="py-20 bg-gray-50 dark:bg-gray-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-navy dark:text-white mb-3" x-text="lang==='ar'?'كل ما يحتاجه مكتبك في منصة واحدة':'Everything your office needs in one platform'"></h2>
            <p class="text-gray-500 dark:text-gray-400" x-text="lang==='ar'?'أدوات متكاملة لإدارة القضايا والعملاء والمالية باحترافية':'Integrated tools to manage cases, clients, and finances professionally'"></p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($features as $feature)
            <div class="scroll-animate opacity-0 bg-white dark:bg-gray-900 rounded-2xl p-7 shadow-sm hover:shadow-lg transition-shadow border border-gray-100 dark:border-gray-800">
                <div class="w-12 h-12 rounded-xl bg-navy/5 dark:bg-gold/10 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-navy dark:text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="{{ $featureIcons[$feature['icon'] ?? ''] ?? $featureIcons['document'] }}"/></svg>
                </div>
                <h3 class="text-lg font-bold text-navy dark:text-white mb-2" x-text="lang==='ar'?'{{ addslashes($feature['title_ar'] ?? '') }}':'{{ addslashes($feature['title_en'] ?? '') }}'"></h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed" x-text="lang==='ar'?'{{ addslashes($feature['desc_ar'] ?? '') }}':'{{ addslashes($feature['desc_en'] ?? '') }}'"></p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Why Us -->
<section id="why-us" class="py-20 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-navy dark:text-white mb-3" x-text="lang==='ar'?'لماذا تختار ميزان؟':'Why choose Mizan?'"></h2>
            <p class="text-gray-500 dark:text-gray-400" x-text="lang==='ar'?'أسباب تجعل ميزان الخيار الأمثل لمكتبك':'Reasons that make Mizan the ideal choice for your office'"></p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($whyUs as $item)
            <div class="scroll-animate opacity-0 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gold/10 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="{{ $whyIcons[$item['icon'] ?? ''] ?? $whyIcons['star'] }}"/></svg>
                </div>
                <h3 class="text-lg font-bold text-navy dark:text-white mb-2" x-text="lang==='ar'?'{{ addslashes($item['title_ar'] ?? '') }}':'{{ addslashes($item['title_en'] ?? '') }}'"></h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed" x-text="lang==='ar'?'{{ addslashes($item['desc_ar'] ?? '') }}':'{{ addslashes($item['desc_en'] ?? '') }}'"></p>
            </div>
            @endforeach
        </div>
    </div>
</section>

@if($homepageVis['show_before_after'] ?? true)
<!-- Before / After -->
<section class="py-20 bg-navy dark:bg-gray-950 overflow-hidden">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14 scroll-animate opacity-0">
            <span class="inline-block text-gold font-semibold text-sm uppercase tracking-widest mb-3" x-text="lang==='ar'?'الفرق الحقيقي':'The Real Difference'"></span>
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4" style="font-family:'Playfair Display',serif;"
                x-text="lang==='ar'?'قبل ميزان وبعد ميزان':'Before Mizan vs After Mizan'"></h2>
            <p class="text-gray-400 max-w-xl mx-auto"
               x-text="lang==='ar'?'هل تعاني من هذه المشاكل؟ لدينا الحل.':'Are you facing these problems? We have the solution.'"></p>
        </div>

        @php
        $comparisons = [
            ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
             'before_ar' => 'ملفات ورقية متناثرة وضياع المستندات',
             'before_en' => 'Scattered paper files and lost documents',
             'after_ar'  => 'كل الوثائق محفوظة ومنظمة بالسحابة',
             'after_en'  => 'All documents stored and organized in the cloud'],
            ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
             'before_ar' => 'نسيان مواعيد الجلسات وتضارب المواعيد',
             'before_en' => 'Forgotten hearings and scheduling conflicts',
             'after_ar'  => 'تذكيرات تلقائية قبل كل جلسة',
             'after_en'  => 'Automatic reminders before every hearing'],
            ['icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
             'before_ar' => 'فوترة يدوية وتتبع المدفوعات في Excel',
             'before_en' => 'Manual invoicing and tracking payments in Excel',
             'after_ar'  => 'فواتير احترافية وتتبع المدفوعات تلقائياً',
             'after_en'  => 'Professional invoices and automatic payment tracking'],
            ['icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2a4 4 0 10-4-4 4 4 0 004 4z',
             'before_ar' => 'العملاء لا يعرفون آخر تطورات قضاياهم',
             'before_en' => 'Clients unaware of case progress',
             'after_ar'  => 'بوابة عملاء متكاملة لمتابعة القضايا',
             'after_en'  => 'Full client portal to follow up on cases'],
            ['icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
             'before_ar' => 'لا يوجد تحليل أو ملخص للعقود والوثائق',
             'before_en' => 'No analysis or summary of contracts and documents',
             'after_ar'  => 'ذكاء اصطناعي يلخص ويحلل فوراً',
             'after_en'  => 'AI that summarizes and analyzes instantly'],
        ];
        @endphp

        <div class="space-y-4">
            @foreach($comparisons as $i => $item)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-0 rounded-2xl overflow-hidden scroll-animate opacity-0"
                 style="animation-delay: {{ $i * 0.08 }}s;">

                {{-- Before --}}
                <div class="flex items-start gap-4 bg-white/5 border border-white/10 px-6 py-4 sm:rounded-s-2xl">
                    <div class="w-8 h-8 rounded-lg bg-red-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs text-red-400 font-semibold uppercase tracking-wider block mb-1"
                              x-text="lang==='ar'?'بدون ميزان':'Without Mizan'"></span>
                        <p class="text-gray-300 text-sm leading-relaxed"
                           x-text="lang==='ar'?'{{ addslashes($item['before_ar']) }}':'{{ addslashes($item['before_en']) }}'"></p>
                    </div>
                </div>

                {{-- After --}}
                <div class="flex items-start gap-4 bg-gold/10 border border-gold/20 px-6 py-4 sm:rounded-e-2xl">
                    <div class="w-8 h-8 rounded-lg bg-gold/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs text-gold font-semibold uppercase tracking-wider block mb-1"
                              x-text="lang==='ar'?'مع ميزان':'With Mizan'"></span>
                        <p class="text-white text-sm leading-relaxed font-medium"
                           x-text="lang==='ar'?'{{ addslashes($item['after_ar']) }}':'{{ addslashes($item['after_en']) }}'"></p>
                    </div>
                </div>

            </div>
            @endforeach
        </div>

        {{-- CTA --}}
        <div class="text-center mt-12 scroll-animate opacity-0">
            <a href="{{ route('register.plans') }}"
               class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gold hover:bg-gold-dark text-white font-bold text-lg transition-all duration-300 shadow-lg shadow-gold/25 hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span x-text="lang==='ar'?'ابدأ تجربتك المجانية الآن':'Start Your Free Trial Now'"></span>
            </a>
            <p class="text-gray-500 text-sm mt-3"
               x-text="lang==='ar'?'شهر كامل مجاناً — بدون بطاقة دفع':'Full month free — no credit card'"></p>
        </div>

    </div>
</section>

@endif

<!-- Pricing -->
<section class="py-20 bg-gray-50 dark:bg-gray-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-navy dark:text-white mb-3" x-text="lang==='ar'?'خطط تناسب كل مكتب':'Plans for every office'"></h2>
            <p class="text-gray-500 dark:text-gray-400" x-text="lang==='ar'?'ابدأ بتجربة مجانية شهر كامل، ثم اختر ما يناسبك':'Start with a free month, then choose what fits'"></p>
        </div>
        @include('saas.partials.plans-grid', ['plans' => $plans])
    </div>
</section>

<!-- Contact -->
<section id="contact" class="py-20 bg-white dark:bg-gray-950">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14 scroll-animate opacity-0">
            <span class="inline-block text-gold font-semibold text-sm uppercase tracking-widest mb-3"
                  x-text="lang==='ar'?'نحن هنا':'We Are Here'"></span>
            <h2 class="text-3xl sm:text-4xl font-bold text-navy dark:text-white mb-3"
                style="font-family:'Playfair Display',serif;"
                x-text="lang==='ar'?'تواصل معنا':'Get in Touch'"></h2>
            <p class="text-gray-500 dark:text-gray-400"
               x-text="lang==='ar'?'هل لديك سؤال؟ فريقنا جاهز لمساعدتك في أي وقت':'Have a question? Our team is ready to help you anytime'"></p>
        </div>

        {{-- Main card --}}
        <div class="rounded-3xl overflow-hidden shadow-2xl shadow-navy/10 dark:shadow-black/40 scroll-animate opacity-0">
            <div class="grid grid-cols-1 lg:grid-cols-5">

                {{-- ===== Contact Info Panel (navy bg) ===== --}}
                <div class="lg:col-span-2 bg-navy relative flex flex-col justify-between p-8 sm:p-10 overflow-hidden">

                    {{-- Decorative circles --}}
                    <div class="absolute -top-16 -end-16 w-56 h-56 rounded-full bg-white/5 pointer-events-none"></div>
                    <div class="absolute -bottom-20 -start-10 w-72 h-72 rounded-full bg-white/5 pointer-events-none"></div>

                    <div class="relative z-10">
                        <h3 class="text-2xl font-bold text-white mb-2"
                            x-text="lang==='ar'?'معلومات التواصل':'Contact Information'"></h3>
                        <p class="text-white/50 text-sm mb-8"
                           x-text="lang==='ar'?'تواصل معنا عبر أي من القنوات التالية':'Reach us through any of the following channels'"></p>

                        <div class="space-y-6">
                            @if(!empty($contact['phone']))
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-white/50 text-xs mb-0.5" x-text="lang==='ar'?'الهاتف':'Phone'"></div>
                                    <a href="tel:{{ $contact['phone'] }}" class="text-white text-sm font-medium hover:text-gold transition-colors" dir="ltr">{{ $contact['phone'] }}</a>
                                </div>
                            </div>
                            @endif

                            @if(!empty($contact['email']))
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-white/50 text-xs mb-0.5" x-text="lang==='ar'?'البريد الإلكتروني':'Email'"></div>
                                    <a href="mailto:{{ $contact['email'] }}" class="text-white text-sm font-medium hover:text-gold transition-colors" dir="ltr">{{ $contact['email'] }}</a>
                                </div>
                            </div>
                            @endif

                            @if(!empty($contact['whatsapp']))
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gold" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-white/50 text-xs mb-0.5">WhatsApp</div>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact['whatsapp']) }}" target="_blank"
                                       class="text-white text-sm font-medium hover:text-gold transition-colors"
                                       x-text="lang==='ar'?'تواصل عبر واتساب':'Chat on WhatsApp'"></a>
                                </div>
                            </div>
                            @endif

                            @if(!empty($contact['address_ar'] ?? $contact['address_en'] ?? null))
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-white/50 text-xs mb-0.5" x-text="lang==='ar'?'العنوان':'Address'"></div>
                                    <div class="text-white text-sm font-medium leading-relaxed"
                                         x-text="lang==='ar'?'{{ addslashes($contact['address_ar'] ?? '') }}':'{{ addslashes($contact['address_en'] ?? '') }}'"></div>
                                </div>
                            </div>
                            @endif

                            @if(!empty($contact['working_hours_ar'] ?? null))
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-white/50 text-xs mb-0.5" x-text="lang==='ar'?'ساعات العمل':'Working Hours'"></div>
                                    <div class="text-white text-sm font-medium"
                                         x-text="lang==='ar'?'{{ addslashes($contact['working_hours_ar'] ?? '') }}':'{{ addslashes($contact['working_hours_en'] ?? $contact['working_hours_ar'] ?? '') }}'"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Bottom CTA hint --}}
                    <div class="relative z-10 mt-10 pt-8 border-t border-white/10">
                        <a href="{{ route('register.plans') }}"
                           class="inline-flex items-center gap-2 text-gold hover:text-gold-light text-sm font-semibold transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span x-text="lang==='ar'?'ابدأ تجربتك المجانية الآن ←':'Start your free trial now →'"></span>
                        </a>
                    </div>
                </div>

                {{-- ===== Contact Form Panel ===== --}}
                <div
                    x-data="{
                        form: { name:'', email:'', phone:'', subject:'', message:'' },
                        loading:false, success:false, error:'',
                        async submit() {
                            this.loading = true; this.error = '';
                            try {
                                const res = await fetch('{{ route('platform.contact') }}', {
                                    method:'POST',
                                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},
                                    body: JSON.stringify(this.form)
                                });
                                const data = await res.json();
                                if (data.success) { this.success = true; this.form = {name:'',email:'',phone:'',subject:'',message:''}; }
                                else { this.error = data.message || (lang==='ar'?'حدث خطأ':'An error occurred'); }
                            } catch(e) { this.error = lang==='ar'?'فشل الاتصال بالخادم':'Connection failed'; }
                            this.loading = false;
                        }
                    }"
                    class="lg:col-span-3 bg-white dark:bg-gray-900 p-8 sm:p-10"
                >
                    {{-- Success state --}}
                    <div x-show="success" x-cloak class="h-full flex flex-col items-center justify-center text-center py-16">
                        <div class="w-20 h-20 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center mx-auto mb-5">
                            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-navy dark:text-white mb-3"
                            x-text="lang==='ar'?'تم الإرسال بنجاح!':'Message Sent Successfully!'"></h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-6 max-w-xs"
                           x-text="lang==='ar'?'سنتواصل معك في أقرب وقت ممكن':'We will contact you as soon as possible'"></p>
                        <button @click="success=false"
                                class="px-5 py-2.5 rounded-xl border border-navy/20 dark:border-white/20 text-navy dark:text-white text-sm font-medium hover:bg-navy hover:text-white dark:hover:bg-white dark:hover:text-navy transition-all"
                                x-text="lang==='ar'?'إرسال رسالة أخرى':'Send Another Message'"></button>
                    </div>

                    {{-- Form --}}
                    <form x-show="!success" @submit.prevent="submit()" class="h-full flex flex-col">
                        <div class="mb-7">
                            <h3 class="text-2xl font-bold text-navy dark:text-white mb-1"
                                x-text="lang==='ar'?'أرسل رسالتك':'Send Your Message'"></h3>
                            <p class="text-gray-400 dark:text-gray-500 text-sm"
                               x-text="lang==='ar'?'سنرد عليك في أقرب وقت ممكن':'We will get back to you as soon as possible'"></p>
                        </div>

                        <div class="flex-1 space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2"
                                           x-text="lang==='ar'?'الاسم الكامل *':'Full Name *'"></label>
                                    <input x-model="form.name" type="text" required
                                           :placeholder="lang==='ar'?'اسمك الكامل':'Your full name'"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gold/30 focus:border-gold transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2"
                                           x-text="lang==='ar'?'البريد الإلكتروني *':'Email *'"></label>
                                    <input x-model="form.email" type="email" required dir="ltr"
                                           :placeholder="lang==='ar'?'بريدك الإلكتروني':'your@email.com'"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gold/30 focus:border-gold transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2"
                                           x-text="lang==='ar'?'رقم الهاتف (اختياري)':'Phone (Optional)'"></label>
                                    <input x-model="form.phone" type="tel" dir="ltr"
                                           :placeholder="lang==='ar'?'رقم هاتفك':'+966 5x xxx xxxx'"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gold/30 focus:border-gold transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2"
                                           x-text="lang==='ar'?'الموضوع *':'Subject *'"></label>
                                    <input x-model="form.subject" type="text" required
                                           :placeholder="lang==='ar'?'موضوع رسالتك':'Message subject'"
                                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gold/30 focus:border-gold transition">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2"
                                       x-text="lang==='ar'?'رسالتك *':'Message *'"></label>
                                <textarea x-model="form.message" required rows="5"
                                          :placeholder="lang==='ar'?'اكتب رسالتك أو استفسارك هنا...':'Write your message or inquiry here...'"
                                          class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gold/30 focus:border-gold transition resize-none"></textarea>
                            </div>
                        </div>

                        <div x-show="error" x-cloak
                             class="mt-4 p-3.5 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm"
                             x-text="error"></div>

                        <button type="submit" :disabled="loading"
                                class="mt-6 w-full py-4 rounded-xl bg-navy hover:bg-navy-light text-white font-bold text-sm transition-all duration-300 disabled:opacity-60 flex items-center justify-center gap-2 shadow-lg shadow-navy/20">
                            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span x-text="loading ? (lang==='ar'?'جاري الإرسال...':'Sending...') : (lang==='ar'?'إرسال الرسالة':'Send Message')"></span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Map strip at bottom --}}
            @if($mapSrc)
            <div class="h-64 lg:h-72">
                <iframe src="{{ $mapSrc }}" width="100%" height="100%" style="border:0; display:block;"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            @endif
        </div>

    </div>
</section>

@if($homepageVis['show_trust_badges'] ?? true)
<!-- Trust Badges -->
<section class="py-14 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-10 scroll-animate opacity-0">
            <p class="text-gray-400 text-sm font-medium uppercase tracking-widest"
               x-text="lang==='ar'?'نلتزم بتقديم أفضل تجربة':'We are committed to the best experience'"></p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Guarantee --}}
            <div class="scroll-animate opacity-0 text-center group" style="animation-delay:.05s;">
                <div class="w-16 h-16 rounded-2xl bg-green-50 dark:bg-green-500/10 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h4 class="font-bold text-navy dark:text-white text-sm mb-1"
                    x-text="lang==='ar'?'ضمان استرداد 14 يوم':'14-Day Money Back'"></h4>
                <p class="text-gray-400 text-xs leading-relaxed"
                   x-text="lang==='ar'?'غير راضٍ؟ نُعيد لك المبلغ كاملاً بدون أسئلة':'Not satisfied? Full refund, no questions asked'"></p>
            </div>

            {{-- Encryption --}}
            <div class="scroll-animate opacity-0 text-center group" style="animation-delay:.1s;">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h4 class="font-bold text-navy dark:text-white text-sm mb-1"
                    x-text="lang==='ar'?'تشفير 256-bit':'256-bit Encryption'"></h4>
                <p class="text-gray-400 text-xs leading-relaxed"
                   x-text="lang==='ar'?'بياناتك محمية بأعلى معايير التشفير العالمية':'Your data protected with the highest encryption standards'"></p>
            </div>

            {{-- No contracts --}}
            <div class="scroll-animate opacity-0 text-center group" style="animation-delay:.15s;">
                <div class="w-16 h-16 rounded-2xl bg-gold/10 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h4 class="font-bold text-navy dark:text-white text-sm mb-1"
                    x-text="lang==='ar'?'لا عقود طويلة الأمد':'No Long-term Contracts'"></h4>
                <p class="text-gray-400 text-xs leading-relaxed"
                   x-text="lang==='ar'?'اشترك شهرياً وألغِ في أي وقت بنقرة واحدة':'Subscribe monthly, cancel anytime with one click'"></p>
            </div>

            {{-- Support --}}
            <div class="scroll-animate opacity-0 text-center group" style="animation-delay:.2s;">
                <div class="w-16 h-16 rounded-2xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 5.636a9 9 0 010 12.728M5.636 18.364a9 9 0 010-12.728m12.728 0A9 9 0 0112 21a9 9 0 01-6.364-2.636M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h4 class="font-bold text-navy dark:text-white text-sm mb-1"
                    x-text="lang==='ar'?'دعم فني 24/7':'24/7 Technical Support'"></h4>
                <p class="text-gray-400 text-xs leading-relaxed"
                   x-text="lang==='ar'?'فريق دعم متخصص جاهز لمساعدتك في أي وقت':'Dedicated support team ready to help anytime'"></p>
            </div>

        </div>

        {{-- Money back banner --}}
        <div class="mt-10 scroll-animate opacity-0 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-500/10 dark:to-emerald-500/10 border border-green-200 dark:border-green-500/20 rounded-2xl p-6 flex flex-col sm:flex-row items-center gap-4 text-center sm:text-start">
            <div class="w-14 h-14 rounded-xl bg-green-100 dark:bg-green-500/20 flex items-center justify-center flex-shrink-0 mx-auto sm:mx-0">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-green-800 dark:text-green-400 mb-1"
                    x-text="lang==='ar'?'ضمان استرداد المبلغ كاملاً خلال 14 يوماً':'14-Day Full Money-Back Guarantee'"></h4>
                <p class="text-green-700 dark:text-green-500 text-sm"
                   x-text="lang==='ar'?'جرّب ميزان بثقة تامة. إذا لم تكن راضياً خلال 14 يوماً من الاشتراك المدفوع، نُعيد لك المبلغ بالكامل فوراً وبدون أسئلة.':'Try Mizan with complete confidence. If you are not satisfied within 14 days of your paid subscription, we will refund you immediately, no questions asked.'"></p>
            </div>
        </div>

    </div>
</section>

@endif

<!-- CTA -->
<section class="py-16 bg-navy dark:bg-gray-950 text-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4" x-text="lang==='ar'?'جاهز لتطوير إدارة مكتبك؟':'Ready to upgrade your office?'"></h2>
        <p class="text-gray-200 mb-8" x-text="lang==='ar'?'ابدأ تجربتك المجانية الآن — شهر كامل بكل المميزات وبدون بطاقة دفع.':'Start your free trial now — a full month with all features and no credit card.'"></p>
        <a href="{{ route('register.plans') }}" class="inline-block px-10 py-4 rounded-xl bg-gold hover:bg-gold-dark text-white font-bold text-lg transition-colors shadow-lg" x-text="lang==='ar'?'ابدأ مجاناً الآن':'Start Free Now'"></a>
    </div>
</section>
@endsection
