@extends('layouts.saas')

@section('title', 'خطأ في الخادم — ميزان')
@section('meta_robots')<meta name="robots" content="noindex,nofollow">@endsection

@section('content')
<section class="min-h-screen bg-navy flex items-center justify-center px-4" dir="rtl">
    <div class="text-center max-w-lg mx-auto">

        {{-- Icon --}}
        <div class="relative inline-flex items-center justify-center w-32 h-32 mx-auto mb-8">
            <div class="absolute inset-0 rounded-full bg-orange-500/10 animate-ping" style="animation-duration:3s"></div>
            <div class="relative w-32 h-32 rounded-full bg-navy-light border border-orange-500/30 flex items-center justify-center">
                <svg class="w-14 h-14 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>

        {{-- Code --}}
        <p class="text-orange-400 font-black text-8xl leading-none mb-4 select-none" style="font-feature-settings:'tnum'">500</p>

        {{-- Message --}}
        <h1 class="text-white text-2xl font-bold mb-3">خطأ في الخادم</h1>
        <p class="text-white/50 text-base mb-10 leading-relaxed">
            حدث خطأ غير متوقع من جانبنا.<br>
            فريقنا التقني أُبلغ تلقائياً وسيعمل على الإصلاح في أقرب وقت.
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
            <button onclick="window.location.reload()"
                    class="inline-flex items-center gap-2 px-8 py-3 rounded-xl border border-white/20 text-white/70 hover:bg-white/5 font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                إعادة المحاولة
            </button>
        </div>

    </div>
</section>
@endsection
