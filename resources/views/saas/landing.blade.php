@extends('layouts.saas')

@section('content')
@php
    $hero  = $platform['hero'] ?? [];
    $stats = $platform['stats'] ?? [];
    $features = $platform['features'] ?? [];

    $icons = [
        'scale'    => 'M12 3v18M5 8l-3 5h6L5 8zm14 0l-3 5h6l-3-5zM5 21h14',
        'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'users'    => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2a4 4 0 10-4-4 4 4 0 004 4z',
        'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'cash'     => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        'sparkles' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
    ];
@endphp

<!-- Hero -->
<section class="relative bg-navy text-white overflow-hidden">
    <div class="absolute inset-0 opacity-10" aria-hidden="true">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="geo" x="0" y="0" width="80" height="80" patternUnits="userSpaceOnUse">
                    <polygon points="40,5 75,25 75,55 40,75 5,55 5,25" fill="none" stroke="#C9A84C" stroke-width="0.5"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#geo)"/>
        </svg>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32 text-center">
        <h1 class="text-3xl sm:text-5xl font-extrabold leading-tight mb-6">{{ $hero['heading_ar'] ?? 'منصة ميزان' }}</h1>
        <p class="text-lg text-gray-200 max-w-3xl mx-auto mb-10">{{ $hero['subtitle_ar'] ?? '' }}</p>
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('register.plans') }}" class="px-8 py-3 rounded-xl bg-gold hover:bg-gold-dark text-white font-bold text-lg transition-colors shadow-lg">
                {{ $hero['cta_ar'] ?? 'ابدأ مجاناً' }} — شهر كامل
            </a>
            <a href="{{ route('pricing') }}" class="px-8 py-3 rounded-xl border border-white/30 hover:border-gold hover:text-gold text-white font-medium text-lg transition-colors">
                عرض الأسعار
            </a>
        </div>
        <p class="text-sm text-gray-400 mt-4">بدون بطاقة دفع — كل المميزات لمدة 30 يوم</p>
    </div>
</section>

<!-- Stats -->
<section class="bg-navy-dark text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        @foreach($stats as $stat)
        <div>
            <div class="text-4xl font-extrabold text-gold">{{ $stat['value'] ?? '' }}{{ $stat['suffix'] ?? '' }}</div>
            <div class="text-sm text-gray-300 mt-2">{{ $stat['label_ar'] ?? '' }}</div>
        </div>
        @endforeach
    </div>
</section>

<!-- Features -->
<section id="features" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-navy mb-3">كل ما يحتاجه مكتبك في منصة واحدة</h2>
            <p class="text-gray-500">أدوات متكاملة لإدارة القضايا والعملاء والمالية باحترافية</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($features as $feature)
            <div class="bg-white rounded-2xl p-7 shadow-sm hover:shadow-lg transition-shadow border border-gray-100">
                <div class="w-12 h-12 rounded-xl bg-navy/5 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="{{ $icons[$feature['icon'] ?? ''] ?? $icons['document'] }}"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-navy mb-2">{{ $feature['title_ar'] ?? '' }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $feature['desc_ar'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Pricing -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-navy mb-3">خطط تناسب كل مكتب</h2>
            <p class="text-gray-500">ابدأ بتجربة مجانية شهر كامل، ثم اختر ما يناسبك</p>
        </div>
        @include('saas.partials.plans-grid', ['plans' => $plans])
    </div>
</section>

<!-- CTA -->
<section class="py-16 bg-navy text-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">جاهز لتطوير إدارة مكتبك؟</h2>
        <p class="text-gray-200 mb-8">ابدأ تجربتك المجانية الآن — شهر كامل بكل المميزات وبدون بطاقة دفع.</p>
        <a href="{{ route('register.plans') }}" class="inline-block px-10 py-4 rounded-xl bg-gold hover:bg-gold-dark text-white font-bold text-lg transition-colors shadow-lg">
            ابدأ مجاناً الآن
        </a>
    </div>
</section>
@endsection
