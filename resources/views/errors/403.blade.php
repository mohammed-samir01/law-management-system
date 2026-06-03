@extends('layouts.saas')

@section('title', 'غير مصرح — ميزان')
@section('meta_robots')<meta name="robots" content="noindex,nofollow">@endsection

@section('content')
<section class="min-h-screen bg-navy flex items-center justify-center px-4" dir="rtl">
    <div class="text-center max-w-lg mx-auto">

        {{-- Icon --}}
        <div class="relative inline-flex items-center justify-center w-32 h-32 mx-auto mb-8">
            <div class="absolute inset-0 rounded-full bg-red-500/10 animate-ping" style="animation-duration:3s"></div>
            <div class="relative w-32 h-32 rounded-full bg-navy-light border border-red-500/30 flex items-center justify-center">
                <svg class="w-14 h-14 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
        </div>

        {{-- Code --}}
        <p class="text-red-400 font-black text-8xl leading-none mb-4 select-none" style="font-feature-settings:'tnum'">403</p>

        {{-- Message --}}
        <h1 class="text-white text-2xl font-bold mb-3">غير مصرح بالوصول</h1>
        <p class="text-white/50 text-base mb-10 leading-relaxed">
            ليس لديك صلاحية للوصول إلى هذه الصفحة.<br>
            إذا كنت تعتقد أن هذا خطأ، تواصل مع مسؤول النظام.
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-gold hover:bg-gold/90 text-white font-bold transition-colors shadow-lg shadow-gold/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                الصفحة الرئيسية
            </a>
            <button onclick="history.back()"
                    class="inline-flex items-center gap-2 px-8 py-3 rounded-xl border border-white/20 text-white/70 hover:bg-white/5 font-medium transition-colors">
                <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
                الصفحة السابقة
            </button>
        </div>

    </div>
</section>
@endsection
