@extends('layouts.saas')

@section('title', 'تفعيل البريد الإلكتروني — ميزان')

@section('content')
<section class="py-20 bg-gray-50 dark:bg-gray-950 min-h-screen">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8 text-center">
            <div class="w-16 h-16 rounded-full bg-gold/10 flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>

            <h1 class="text-2xl font-bold text-navy dark:text-white mb-2">تفعيل البريد الإلكتروني</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">
                أرسلنا رمزاً مكوّناً من أرقام إلى بريدك <strong dir="ltr">{{ auth()->user()?->email }}</strong>. أدخله بالأسفل للمتابعة.
            </p>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-3 mb-4 text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-3 mb-4 text-sm">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('verification.verify') }}" class="space-y-4">
                @csrf
                <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" required autofocus
                       class="w-full text-center tracking-[0.5em] text-2xl font-bold rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 px-3 py-3 focus:border-gold focus:ring-1 focus:ring-gold outline-none"
                       placeholder="------" dir="ltr">
                <button type="submit" class="w-full py-3 rounded-xl bg-navy hover:bg-navy-dark text-white font-bold transition-colors">
                    تفعيل الحساب
                </button>
            </form>

            <form method="POST" action="{{ route('verification.resend') }}" class="mt-4">
                @csrf
                <button type="submit" class="text-gold hover:underline text-sm">لم يصلك الرمز؟ إعادة الإرسال</button>
            </form>

            @if(\Illuminate\Support\Facades\Route::has('filament.admin.auth.logout'))
            <form method="POST" action="{{ route('filament.admin.auth.logout') }}" class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-gray-600 text-xs">تسجيل الخروج</button>
            </form>
            @endif
        </div>
    </div>
</section>
@endsection
