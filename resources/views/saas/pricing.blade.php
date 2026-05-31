@extends('layouts.saas')

@section('title', 'الأسعار — ميزان')

@section('content')
<section class="py-20 bg-gray-50 min-h-screen" x-data="{ cycle: 'monthly' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-navy mb-3">خطط وأسعار ميزان</h1>
            <p class="text-gray-500">جميع الأسعار بالجنيه المصري — ابدأ بتجربة مجانية شهر كامل</p>
        </div>

        <!-- Cycle toggle -->
        <div class="flex items-center justify-center gap-3 mb-12">
            <button @click="cycle='monthly'" :class="cycle==='monthly' ? 'bg-navy text-white' : 'bg-white text-gray-500'" class="px-5 py-2 rounded-lg text-sm font-bold transition-colors border border-gray-200">شهري</button>
            <button @click="cycle='yearly'" :class="cycle==='yearly' ? 'bg-navy text-white' : 'bg-white text-gray-500'" class="px-5 py-2 rounded-lg text-sm font-bold transition-colors border border-gray-200">
                سنوي <span class="text-gold text-xs">(وفّر شهرين)</span>
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
