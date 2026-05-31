@php $platform = $platform ?? \App\Models\PlatformSetting::current(); @endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($platform['brand']['name_ar'] ?? 'ميزان') . ' — منصة إدارة مكاتب المحاماة')</title>
    <meta name="description" content="@yield('description', $platform['hero']['subtitle_ar'] ?? 'منصة ميزان لإدارة مكاتب المحاماة')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Tajawal', sans-serif; }
        [x-cloak] { display: none !important; }
        :root {
            --color-navy:       #1E3A5F;
            --color-navy-light: color-mix(in srgb, #1E3A5F 80%, white);
            --color-navy-dark:  color-mix(in srgb, #1E3A5F 80%, black);
            --color-gold:       #C9A84C;
            --color-gold-light: color-mix(in srgb, #C9A84C 80%, white);
            --color-gold-dark:  color-mix(in srgb, #C9A84C 80%, black);
        }
    </style>
</head>
<body class="bg-white text-gray-800 antialiased">

@php
    $brandName = $platform['brand']['name_ar'] ?? 'ميزان';
    $logoPath  = $platform['brand']['logo_path'] ?? null;
    $logoUrl   = $logoPath ? (str_starts_with($logoPath, 'http') ? $logoPath : asset('storage/' . $logoPath)) : null;
@endphp

<nav class="fixed top-0 inset-x-0 z-50 bg-navy/95 backdrop-blur-md shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $brandName }}" class="h-9 w-auto object-contain">
                @else
                    <div class="w-10 h-10 rounded-full bg-gold flex items-center justify-center shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#1E3A5F" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                            <line x1="12" y1="3" x2="12" y2="21"/><path d="M5 21h14"/><path d="M5 8l-3 5h6L5 8z"/><path d="M19 8l-3 5h6l-3-5z"/><line x1="5" y1="8" x2="19" y2="8"/>
                        </svg>
                    </div>
                @endif
                <span class="text-white font-bold text-xl">{{ $brandName }}</span>
            </a>

            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}#features" class="text-sm font-medium text-gray-200 hover:text-gold transition-colors">المميزات</a>
                <a href="{{ route('pricing') }}" class="text-sm font-medium text-gray-200 hover:text-gold transition-colors">الأسعار</a>
                <a href="/admin/login" class="text-sm font-medium text-gray-200 hover:text-gold transition-colors">تسجيل الدخول</a>
            </div>

            <a href="{{ route('register.plans') }}" class="text-sm font-medium px-4 py-2 rounded-lg bg-gold hover:bg-gold-dark text-white transition-colors">
                ابدأ مجاناً
            </a>
        </div>
    </div>
</nav>

<main class="pt-16">
    @yield('content')
</main>

<footer class="bg-navy-dark text-gray-400 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <span class="text-white font-bold">{{ $brandName }}</span>
            <span class="text-sm">— منصة إدارة مكاتب المحاماة</span>
        </div>
        <div class="text-sm">© {{ date('Y') }} {{ $brandName }}. جميع الحقوق محفوظة.</div>
    </div>
</footer>

</body>
</html>
