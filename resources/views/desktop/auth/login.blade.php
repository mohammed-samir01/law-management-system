<!DOCTYPE html>
<html lang="ar" dir="rtl" class="{{ session('theme','light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول — عامر</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>if(localStorage.getItem('amer_theme')==='dark')document.documentElement.classList.add('dark');</script>
</head>
<body class="bg-[#1E3A5F] min-h-screen flex items-center justify-center">

    <div class="w-full max-w-sm mx-4">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-[#C9A84C] rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <span class="text-white font-bold text-3xl">ع</span>
            </div>
            <h1 class="text-white font-bold text-2xl">عامر</h1>
            <p class="text-white/60 text-sm mt-1">إدارة مكاتب المحاماة</p>
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-2xl">
            <h2 class="font-bold text-[#1E3A5F] dark:text-white text-xl mb-6 text-center">تسجيل الدخول</h2>

            <form method="POST" action="{{ route('desktop.login.post') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">
                        البريد الإلكتروني
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200 dark:border-gray-600' }}
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100
                                  rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                           placeholder="admin@amer.test">
                    @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">
                        كلمة المرور
                    </label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-200 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100
                                  rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300">
                        <span class="text-sm text-gray-500 dark:text-gray-400">تذكّرني</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-[#1E3A5F] hover:bg-[#16304f] text-white rounded-xl py-3.5 font-bold text-sm transition-colors">
                    دخول
                </button>
            </form>
        </div>

        <p class="text-white/40 text-xs text-center mt-6">عامر v{{ config('nativephp.version', '1.0.0') }}</p>
    </div>

</body>
</html>
