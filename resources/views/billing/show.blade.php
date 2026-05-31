@extends('layouts.saas')

@section('title', 'الاشتراك والفوترة — ميزان')

@section('content')
<section class="py-16 bg-gray-50 min-h-screen" x-data="{ cycle: 'monthly', planId: null }">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-navy">الاشتراك والفوترة</h1>
            <a href="/admin" class="text-sm text-gray-500 hover:text-navy">← العودة للوحة التحكم</a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6 text-sm">{{ session('error') }}</div>
        @endif

        {{-- Current subscription --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-10">
            <h2 class="font-bold text-navy mb-4">اشتراكك الحالي</h2>
            @if($subscription)
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <div class="text-xs text-gray-400 mb-1">الخطة</div>
                        <div class="font-bold text-navy">{{ $subscription->plan?->getTranslation('name', 'ar') ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-1">الحالة</div>
                        <div class="font-bold {{ $subscription->isUsable() ? 'text-green-600' : 'text-red-600' }}">{{ $subscription->status_label }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-1">الأيام المتبقية</div>
                        <div class="font-bold text-navy">{{ $subscription->daysLeft() }} يوم</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-1">ينتهي في</div>
                        <div class="font-bold text-navy">{{ optional($subscription->onTrial() ? $subscription->trial_ends_at : $subscription->current_period_end)?->format('Y/m/d') ?? '—' }}</div>
                    </div>
                </div>
                @if($subscription->onTrial())
                    <div class="mt-4 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg p-3 text-sm">
                        أنت في الفترة التجريبية المجانية. اختر خطة بالأسفل لتفعيل اشتراكك قبل انتهاء التجربة.
                    </div>
                @endif
            @else
                <p class="text-gray-400 text-sm">لا يوجد اشتراك بعد.</p>
            @endif
        </div>

        {{-- Choose a plan to pay --}}
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-navy mb-2">اختر خطة للاشتراك</h2>
            <div class="flex items-center justify-center gap-3 mt-4">
                <button @click="cycle='monthly'" :class="cycle==='monthly' ? 'bg-navy text-white' : 'bg-white text-gray-500'" class="px-5 py-2 rounded-lg text-sm font-bold border border-gray-200">شهري</button>
                <button @click="cycle='yearly'" :class="cycle==='yearly' ? 'bg-navy text-white' : 'bg-white text-gray-500'" class="px-5 py-2 rounded-lg text-sm font-bold border border-gray-200">سنوي <span class="text-gold text-xs">(وفّر شهرين)</span></button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                @php $isPopular = $plan->slug === 'pro'; @endphp
                <form method="POST" action="{{ route('billing.checkout') }}"
                      class="relative bg-white rounded-2xl border {{ $isPopular ? 'border-gold shadow-xl ring-2 ring-gold/30' : 'border-gray-200 shadow-sm' }} p-7 flex flex-col">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <input type="hidden" name="billing_cycle" :value="cycle">

                    @if($isPopular)
                        <span class="absolute -top-3 right-6 bg-gold text-white text-xs font-bold px-3 py-1 rounded-full">الأكثر شيوعاً</span>
                    @endif

                    <h3 class="text-xl font-bold text-navy mb-1">{{ $plan->getTranslation('name', 'ar') }}</h3>
                    <div class="my-4">
                        <div class="text-3xl font-extrabold text-navy">
                            <span x-show="cycle==='monthly'">{{ number_format($plan->price_monthly) }}</span>
                            <span x-show="cycle==='yearly'" x-cloak>{{ number_format($plan->price_yearly) }}</span>
                            <span class="text-base font-medium text-gray-400">ج.م</span>
                        </div>
                        <div class="text-sm text-gray-400 mt-1"><span x-show="cycle==='monthly'">شهرياً</span><span x-show="cycle==='yearly'" x-cloak>سنوياً</span></div>
                    </div>

                    <ul class="space-y-3 mb-7 flex-1">
                        @foreach(($plan->features ?? []) as $feature)
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>

                    <button type="submit" class="block w-full text-center py-3 rounded-xl font-bold transition-colors {{ $isPopular ? 'bg-gold hover:bg-gold-dark text-white' : 'bg-navy hover:bg-navy-dark text-white' }}">
                        ادفع واشترك
                    </button>
                </form>
            @endforeach
        </div>

        <p class="text-center text-xs text-gray-400 mt-8">الدفع آمن عبر بوابة دفع موثقة. تُحصّل المبالغ بالجنيه المصري.</p>
    </div>
</section>
@endsection
