@extends('layouts.saas')
@section('meta_robots')<meta name="robots" content="noindex,nofollow">@endsection

@section('title', 'تم إنشاء حسابك — ميزان')

@section('content')
<section class="py-20 bg-gray-50 min-h-screen">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">

        @include('onboarding.partials.steps', ['active' => 4])

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">

            {{-- Success icon --}}
            <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-navy mb-2">تم إنشاء مكتبك بنجاح! 🎉</h1>
            <p class="text-gray-500 mb-8">بدأت تجربتك المجانية لمدة <span class="font-bold text-navy">30 يوماً</span> بكل المميزات.</p>

            {{-- Links --}}
            @if($office)
            <div class="space-y-3 mb-8 text-start">

                <div class="flex items-center justify-between gap-3 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-navy/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-400 mb-0.5">موقع مكتبك</p>
                            <p class="text-sm font-medium text-navy truncate" dir="ltr">{{ url('/offices/' . $office->slug) }}</p>
                        </div>
                    </div>
                    <a href="{{ url('/offices/' . $office->slug) }}" target="_blank"
                       class="flex-shrink-0 text-xs px-3 py-1.5 rounded-lg bg-navy text-white hover:bg-navy-dark transition-colors">
                        فتح
                    </a>
                </div>

                <div class="flex items-center justify-between gap-3 bg-gold/5 border border-gold/30 rounded-xl px-4 py-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-gold/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-400 mb-0.5">لوحة التحكم</p>
                            <p class="text-sm font-medium text-navy truncate" dir="ltr">{{ url('/admin') }}</p>
                        </div>
                    </div>
                    <a href="/admin"
                       class="flex-shrink-0 text-xs px-3 py-1.5 rounded-lg bg-gold text-white hover:bg-gold-dark transition-colors">
                        دخول
                    </a>
                </div>

            </div>
            @endif

            <a href="/admin" class="inline-block w-full py-3 rounded-xl bg-navy hover:bg-navy-dark text-white font-bold text-base transition-colors shadow-md">
                ادخل إلى لوحة التحكم ←
            </a>

        </div>
    </div>
</section>
@endsection
