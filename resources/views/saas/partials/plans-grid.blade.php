@php $cycle = $cycle ?? 'monthly'; @endphp
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    @foreach($plans as $plan)
        @php
            $isPopular  = $plan->slug === 'pro';
            $isTrial    = $plan->slug === 'trial';
            $egpPrice   = $cycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly;
            $nameAr     = $plan->getTranslation('name', 'ar');
            $nameEn     = $plan->getTranslation('name', 'en') ?: $nameAr;
            $features   = $plan->features ?? [];

            // Convert price to visitor's currency
            $priceData  = fmt_price((float) $egpPrice);
        @endphp
        <div class="relative bg-white dark:bg-gray-900 rounded-2xl border {{ $isPopular ? 'border-gold shadow-xl ring-2 ring-gold/30' : 'border-gray-200 dark:border-gray-700 shadow-sm' }} p-7 flex flex-col">

            @if($isPopular)
                <span class="absolute -top-3 end-6 bg-gold text-white text-xs font-bold px-3 py-1 rounded-full"
                      x-text="lang==='ar' ? 'الأكثر شيوعاً' : 'Most Popular'"></span>
            @endif

            {{-- Plan name --}}
            <h3 class="text-xl font-bold text-navy dark:text-white mb-1"
                x-text="lang==='ar' ? '{{ addslashes($nameAr) }}' : '{{ addslashes($nameEn) }}'"></h3>

            {{-- Price --}}
            <div class="my-4">
                @if($isTrial)
                    <div class="text-3xl font-extrabold text-navy dark:text-white"
                         x-text="lang==='ar' ? 'مجاناً' : 'Free'"></div>
                    <div class="text-sm text-gray-400 mt-1"
                         x-text="lang==='ar' ? 'لمدة 30 يوم' : 'for 30 days'"></div>
                @else
                    <div class="text-3xl font-extrabold text-navy dark:text-white">
                        <span style="font-variant-numeric:normal; unicode-bidi:isolate; direction:ltr; display:inline-block;">
                            {{ $priceData['amount'] }}
                        </span>
                        <span class="text-base font-medium text-gray-400"
                              x-text="lang==='ar' ? '{{ $priceData['symbol'] }}' : '{{ $priceData['symbol_en'] }}'"></span>
                    </div>
                    <div class="text-sm text-gray-400 mt-1"
                         x-text="lang==='ar'
                             ? '{{ $cycle === 'yearly' ? 'سنوياً' : 'شهرياً' }}'
                             : '{{ $cycle === 'yearly' ? 'per year' : 'per month' }}'"></div>
                @endif
            </div>

            {{-- Features --}}
            <ul class="space-y-3 mb-7 flex-1">
                @foreach($features as $feature)
                <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-300">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    @if(is_array($feature))
                        <span x-text="lang==='ar' ? '{{ addslashes($feature['ar'] ?? '') }}' : '{{ addslashes($feature['en'] ?? $feature['ar'] ?? '') }}'"></span>
                    @else
                        <span>{{ $feature }}</span>
                    @endif
                </li>
                @endforeach
            </ul>

            {{-- CTA --}}
            <a href="{{ route('register.plans') }}"
               class="block text-center py-3 rounded-xl font-bold transition-colors {{ $isPopular ? 'bg-gold hover:bg-gold-dark text-white' : 'bg-navy/5 dark:bg-white/10 hover:bg-navy hover:text-white text-navy dark:text-white' }}"
               x-text="lang==='ar'
                   ? '{{ $isTrial ? 'ابدأ التجربة' : 'اختر الخطة' }}'
                   : '{{ $isTrial ? 'Start Trial' : 'Choose Plan' }}'"></a>
        </div>
    @endforeach
</div>
