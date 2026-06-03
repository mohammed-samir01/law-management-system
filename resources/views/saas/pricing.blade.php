@extends('layouts.saas')

@section('title')
    <span x-text="lang==='ar' ? 'الأسعار — ميزان' : 'Pricing — Mizan'"></span>
@endsection

@section('content')
<section class="py-20 bg-gray-50 min-h-screen" x-data="{ cycle: 'monthly' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-navy mb-3"
                x-text="lang==='ar' ? 'خطط وأسعار ميزان' : 'Plans for every office'"></h1>
            <p class="text-gray-500"
               x-text="lang==='ar'
                   ? 'جميع الأسعار بالجنيه المصري — ابدأ بتجربة مجانية شهر كامل'
                   : 'Start with a free month, then choose what fits'"></p>
        </div>

        <!-- Cycle toggle -->
        <div class="flex items-center justify-center gap-3 mb-12">
            <button @click="cycle='monthly'"
                    :class="cycle==='monthly' ? 'bg-navy text-white' : 'bg-white text-gray-500'"
                    class="px-5 py-2 rounded-lg text-sm font-bold transition-colors border border-gray-200"
                    x-text="lang==='ar' ? 'شهري' : 'Monthly'"></button>
            <button @click="cycle='yearly'"
                    :class="cycle==='yearly' ? 'bg-navy text-white' : 'bg-white text-gray-500'"
                    class="px-5 py-2 rounded-lg text-sm font-bold transition-colors border border-gray-200">
                <span x-text="lang==='ar' ? 'سنوي' : 'Yearly'"></span>
                <span class="text-gold text-xs" x-text="lang==='ar' ? '(وفّر شهرين)' : '(Save 2 months)'"></span>
            </button>
        </div>

        <div x-show="cycle==='monthly'">
            @include('saas.partials.plans-grid', ['plans' => $plans, 'cycle' => 'monthly'])
        </div>
        <div x-show="cycle==='yearly'" x-cloak>
            @include('saas.partials.plans-grid', ['plans' => $plans, 'cycle' => 'yearly'])
        </div>
    </div>
</section>
@endsection
