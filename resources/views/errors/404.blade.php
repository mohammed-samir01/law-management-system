@extends('layouts.saas')

@section('title', 'الصفحة غير موجودة — ميزان')

@section('content')
<section class="min-h-screen bg-navy flex items-center justify-center px-4" dir="rtl">
    <div class="text-center max-w-lg mx-auto">

        {{-- Icon --}}
        <div class="relative inline-flex items-center justify-center w-32 h-32 mx-auto mb-8">
            <div class="absolute inset-0 rounded-full bg-gold/10 animate-ping" style="animation-duration:3s"></div>
            <div class="relative w-32 h-32 rounded-full bg-navy-light border border-gold/30 flex items-center justify-center">
                <svg class="w-14 h-14 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        {{-- Code --}}
        <p class="text-gold font-black text-8xl leading-none mb-4 select-none" style="font-feature-settings:'tnum'">404</p>

        {{-- Message --}}
        <h1 class="text-white text-2xl font-bold mb-3">الصفحة غير موجودة</h1>
        <p class="text-white/50 text-base mb-10 leading-relaxed">
            هذه الصفحة قد تكون محذوفة أو أن الرابط غير صحيح.<br>
            تحقق من الرابط أو عد إلى الصفحة الرئيسية.
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
