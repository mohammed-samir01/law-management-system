@extends('layouts.saas')

@section('title', 'تم إنشاء حسابك — ميزان')

@section('content')
<section class="py-20 bg-gray-50 min-h-screen">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">

        @include('onboarding.partials.steps', ['active' => 3])

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
            <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>

            <h1 class="text-2xl font-bold text-navy mb-3">تم إنشاء مكتبك بنجاح! 🎉</h1>
            <p class="text-gray-500 mb-2">بدأت تجربتك المجانية لمدة <span class="font-bold text-navy">30 يوماً</span> بكل المميزات.</p>
            <p class="text-gray-400 text-sm mb-8">يمكنك الآن الدخول إلى لوحة التحكم وإدارة قضاياك وعملائك.</p>

            <a href="/admin" class="inline-block px-10 py-3 rounded-xl bg-gold hover:bg-gold-dark text-white font-bold text-lg transition-colors shadow-lg">
                ادخل إلى لوحة التحكم
            </a>
        </div>
    </div>
</section>
@endsection
