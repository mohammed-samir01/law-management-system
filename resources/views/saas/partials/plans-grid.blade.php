@php $cycle = $cycle ?? 'monthly'; @endphp
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    @foreach($plans as $plan)
        @php
            $isPopular = $plan->slug === 'pro';
            $isTrial   = $plan->slug === 'trial';
            $price     = $cycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly;
        @endphp
        <div class="relative bg-white rounded-2xl border {{ $isPopular ? 'border-gold shadow-xl ring-2 ring-gold/30' : 'border-gray-200 shadow-sm' }} p-7 flex flex-col">
            @if($isPopular)
                <span class="absolute -top-3 right-6 bg-gold text-white text-xs font-bold px-3 py-1 rounded-full">الأكثر شيوعاً</span>
            @endif

            <h3 class="text-xl font-bold text-navy mb-1">{{ $plan->getTranslation('name', 'ar') }}</h3>

            <div class="my-4">
                @if($isTrial)
                    <div class="text-3xl font-extrabold text-navy">مجاناً</div>
                    <div class="text-sm text-gray-400 mt-1">لمدة 30 يوم</div>
                @else
                    <div class="text-3xl font-extrabold text-navy">{{ number_format($price) }}
                        <span class="text-base font-medium text-gray-400">{{ $plan->currency === 'EGP' ? 'ج.م' : $plan->currency }}</span>
                    </div>
                    <div class="text-sm text-gray-400 mt-1">{{ $cycle === 'yearly' ? 'سنوياً' : 'شهرياً' }}</div>
                @endif
            </div>

            <ul class="space-y-3 mb-7 flex-1">
                @foreach(($plan->features ?? []) as $feature)
                <li class="flex items-start gap-2 text-sm text-gray-600">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ $feature }}</span>
                </li>
                @endforeach
            </ul>

            <a href="{{ route('register.plans') }}"
               class="block text-center py-3 rounded-xl font-bold transition-colors {{ $isPopular ? 'bg-gold hover:bg-gold-dark text-white' : 'bg-navy/5 hover:bg-navy hover:text-white text-navy' }}">
                {{ $isTrial ? 'ابدأ التجربة' : 'اختر الخطة' }}
            </a>
        </div>
    @endforeach
</div>
