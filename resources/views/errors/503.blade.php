@extends('layouts.saas')

@section('title', 'الموقع تحت الصيانة — ميزان')
@section('meta_robots')<meta name="robots" content="noindex,nofollow">@endsection

@section('content')
<section class="min-h-screen bg-navy flex items-center justify-center px-4" dir="rtl">
    <div class="text-center max-w-lg mx-auto">

        {{-- Icon --}}
        <div class="relative inline-flex items-center justify-center w-32 h-32 mx-auto mb-8">
            <div class="absolute inset-0 rounded-full bg-blue-400/10 animate-ping" style="animation-duration:3s"></div>
            <div class="relative w-32 h-32 rounded-full bg-navy-light border border-blue-400/30 flex items-center justify-center">
                <svg class="w-14 h-14 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>

        {{-- Code --}}
        <p class="text-blue-400 font-black text-8xl leading-none mb-4 select-none" style="font-feature-settings:'tnum'">503</p>

        {{-- Message --}}
        <h1 class="text-white text-2xl font-bold mb-3">الموقع تحت الصيانة</h1>
        <p class="text-white/50 text-base mb-4 leading-relaxed">
            نعمل حالياً على تحسين المنصة لنقدم لك تجربة أفضل.<br>
            سنعود قريباً — شكراً لصبرك.
        </p>

        @if(isset($exception) && method_exists($exception, 'getMessage') && app()->isLocal())
        <p class="text-white/30 text-xs mb-6 font-mono">{{ $exception->getMessage() }}</p>
        @endif

        {{-- Retry --}}
        <button onclick="window.location.reload()"
                class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-gold hover:bg-gold/90 text-white font-bold transition-colors shadow-lg shadow-gold/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            تحديث الصفحة
        </button>

    </div>
</section>
@endsection
