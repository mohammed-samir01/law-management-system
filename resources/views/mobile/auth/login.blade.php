<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>تسجيل الدخول — عامر</title>
    <script>if(localStorage.getItem('amer_theme')==='dark')document.documentElement.classList.add('dark');</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Tajawal', sans-serif; }
        .safe-top { padding-top: env(safe-area-inset-top); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
    </style>
</head>
<body class="bg-[#1E3A5F] dark:bg-gray-950 min-h-screen flex flex-col" x-data>

    {{-- Logo Area --}}
    <div class="flex-1 flex flex-col items-center justify-center px-6 safe-top pt-16">
        <div class="mb-8 text-center">
            <div class="w-20 h-20 bg-[#C9A84C] rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl">
                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">عامر</h1>
            <p class="text-[#C9A84C] text-sm mt-1">نظام إدارة مكتب المحاماة</p>
        </div>

        {{-- Card --}}
        <div class="w-full bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-6">
            <h2 class="text-xl font-bold text-[#1E3A5F] dark:text-white mb-6 text-center">تسجيل الدخول</h2>

            @if(session('error'))
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 rounded-xl px-4 py-3 mb-4 text-sm">
                {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('mobile.login.post') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent @error('email') border-red-400 @enderror"
                           placeholder="example@domain.com">
                    @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4" x-data="{ show: false }">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">كلمة المرور</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" required
                               class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F] focus:border-transparent @error('password') border-red-400 @enderror"
                               placeholder="••••••••">
                        <button type="button" @click="show = !show"
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 2FA Code (shown if required) --}}
                @if(session('requires_2fa'))
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">رمز التحقق الثنائي</label>
                    <input type="text" name="two_factor_code" inputmode="numeric" maxlength="6"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl px-4 py-3 text-sm text-center tracking-widest focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                           placeholder="000000">
                </div>
                @endif

                <button type="submit"
                        class="w-full bg-[#1E3A5F] text-white rounded-xl py-3.5 font-bold text-base mt-2 active:opacity-80 transition-opacity">
                    دخول
                </button>
            </form>
        </div>
    </div>

    <div class="text-center text-[#C9A84C] text-xs pb-6 safe-bottom mt-4">
        عامر © {{ date('Y') }}
    </div>

</body>
</html>
