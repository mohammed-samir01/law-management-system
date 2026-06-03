@extends('layouts.saas')
@section('meta_robots')<meta name="robots" content="noindex,nofollow">@endsection

@section('title', 'اختر خطتك — ميزان')

@section('content')
<section class="py-16 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        @include('onboarding.partials.steps', ['active' => 1])

        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-navy mb-2">اختر الخطة المناسبة لمكتبك</h1>
            <p class="text-gray-500">كل الخطط تبدأ بتجربة مجانية شهر كامل — لن تُطالَب بالدفع الآن</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($plans as $plan)
                @php $isPopular = $plan->slug === 'pro'; $isTrial = $plan->slug === 'trial'; @endphp
                <form method="POST" action="{{ route('register.plan.select') }}"
                      class="relative bg-white rounded-2xl border {{ $isPopular ? 'border-gold shadow-xl ring-2 ring-gold/30' : 'border-gray-200 shadow-sm' }} p-7 flex flex-col">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <input type="hidden" name="billing_cycle" value="monthly">

                    @if($isPopular)
                        <span class="absolute -top-3 end-6 bg-gold text-white text-xs font-bold px-3 py-1 rounded-full">الأكثر شيوعاً</span>
                    @endif

                    <h3 class="text-xl font-bold text-navy mb-1">{{ $plan->getTranslation('name', 'ar') }}</h3>
                    <div class="my-4">
                        @if($isTrial)
                            <div class="text-3xl font-extrabold text-navy">مجاناً</div>
                            <div class="text-sm text-gray-400 mt-1">لمدة 30 يوم</div>
                        @else
                            <div class="text-3xl font-extrabold text-navy">{{ number_format($plan->price_monthly) }}
                                <span class="text-base font-medium text-gray-400">ج.م/شهر</span>
                            </div>
                        @endif
                    </div>

                    <ul class="space-y-3 mb-7 flex-1">
                        @foreach(($plan->features ?? []) as $feature)
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ is_array($feature) ? ($feature['ar'] ?? '') : $feature }}</span>
                        </li>
                        @endforeach
                    </ul>

                    <button type="submit" class="block w-full text-center py-3 rounded-xl font-bold transition-colors {{ $isPopular ? 'bg-gold hover:bg-gold-dark text-white' : 'bg-navy/5 hover:bg-navy hover:text-white text-navy' }}">
                        اختر هذه الخطة
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</section>
@endsection
