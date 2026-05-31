@extends('layouts.saas')

@section('title', 'انتهى اشتراكك — ميزان')

@section('content')
<section class="py-20 bg-gray-50 min-h-screen">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
            <div class="w-20 h-20 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>

            <h1 class="text-2xl font-bold text-navy mb-3">انتهى اشتراكك</h1>
            <p class="text-gray-500 mb-8">للاستمرار في استخدام ميزان وإدارة مكتبك، يرجى تجديد اشتراكك باختيار إحدى الخطط.</p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('billing.show') }}" class="inline-block px-8 py-3 rounded-xl bg-gold hover:bg-gold-dark text-white font-bold transition-colors shadow-lg">
                    جدّد اشتراكك
                </a>
                @if(\Illuminate\Support\Facades\Route::has('filament.admin.auth.logout'))
                <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                    @csrf
                    <button type="submit" class="px-8 py-3 rounded-xl border border-gray-200 text-gray-500 hover:text-navy hover:border-navy font-medium transition-colors">
                        تسجيل الخروج
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
