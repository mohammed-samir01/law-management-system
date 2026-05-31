@php $platform = $platform ?? \App\Models\PlatformSetting::current(); @endphp
@php
    $brandAr  = $platform['brand']['name_ar'] ?? 'ميزان';
    $brandEn  = $platform['brand']['name_en'] ?? 'Mizan';
    $logoPath = $platform['brand']['logo_path'] ?? null;
    $logoUrl  = $logoPath ? (str_starts_with($logoPath, 'http') ? $logoPath : asset('storage/' . $logoPath)) : null;
    $contact  = $platform['contact'] ?? [];
@endphp
<!DOCTYPE html>
<html
    x-data="landingApp()"
    :class="{ 'dark': darkMode }"
    :lang="lang"
    :dir="lang === 'ar' ? 'rtl' : 'ltr'"
    lang="ar" dir="rtl"
    class="scroll-smooth"
>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $brandAr . ' — منصة إدارة مكاتب المحاماة')</title>
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
<body class="bg-white dark:bg-gray-950 text-gray-800 dark:text-gray-100 antialiased transition-colors duration-300">

<!-- Navbar -->
<nav
    x-data="{ scrolled: false, open: false }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 40 })"
    :class="scrolled ? 'bg-navy/95 shadow-lg backdrop-blur-md' : 'bg-navy/80 backdrop-blur-sm'"
    class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-18">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="logo" class="h-9 w-auto object-contain">
                @else
                    <div class="w-10 h-10 rounded-full bg-gold flex items-center justify-center shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#1E3A5F" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                            <line x1="12" y1="3" x2="12" y2="21"/><path d="M5 21h14"/><path d="M5 8l-3 5h6L5 8z"/><path d="M19 8l-3 5h6l-3-5z"/><line x1="5" y1="8" x2="19" y2="8"/>
                        </svg>
                    </div>
                @endif
                <span class="text-white font-bold text-xl" x-text="lang === 'ar' ? '{{ $brandAr }}' : '{{ $brandEn }}'"></span>
            </a>

            <!-- Desktop links -->
            <div class="hidden lg:flex items-center gap-8">
                <a href="{{ route('home') }}#features" class="text-sm font-medium text-gray-200 hover:text-gold transition-colors" x-text="lang==='ar'?'المميزات':'Features'"></a>
                <a href="{{ route('home') }}#why-us" class="text-sm font-medium text-gray-200 hover:text-gold transition-colors" x-text="lang==='ar'?'لماذا ميزان':'Why Mizan'"></a>
                <a href="{{ route('pricing') }}" class="text-sm font-medium text-gray-200 hover:text-gold transition-colors" x-text="lang==='ar'?'الأسعار':'Pricing'"></a>
                <a href="{{ route('home') }}#contact" class="text-sm font-medium text-gray-200 hover:text-gold transition-colors" x-text="lang==='ar'?'تواصل معنا':'Contact'"></a>
            </div>

            <!-- Right actions -->
            <div class="hidden lg:flex items-center gap-3">
                <button @click="toggleLang()" class="text-xs font-medium border border-gray-400 text-gray-200 hover:border-gold hover:text-gold rounded-full px-3 py-1 transition-colors" x-text="lang === 'ar' ? 'EN' : 'ع'"></button>
                <button @click="toggleDark()" class="p-1.5 rounded-full text-gray-200 hover:text-gold transition-colors">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                <a href="/admin/login" class="text-sm font-medium text-gray-200 hover:text-gold transition-colors px-3 py-2" x-text="lang==='ar'?'تسجيل الدخول':'Login'"></a>
                <a href="{{ route('register.plans') }}" class="text-sm font-medium px-4 py-2 rounded-lg bg-gold hover:bg-gold-dark text-white transition-colors" x-text="lang==='ar'?'ابدأ مجاناً':'Start Free'"></a>
            </div>

            <!-- Mobile button -->
            <button @click="open = !open" class="lg:hidden p-2 rounded-lg text-gray-200">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" x-cloak class="lg:hidden bg-navy/97 backdrop-blur-md border-t border-white/10">
        <div class="px-4 py-4 space-y-1">
            <a href="{{ route('home') }}#features" @click="open=false" class="block py-2 px-3 text-gray-200 hover:text-gold rounded-lg" x-text="lang==='ar'?'المميزات':'Features'"></a>
            <a href="{{ route('home') }}#why-us" @click="open=false" class="block py-2 px-3 text-gray-200 hover:text-gold rounded-lg" x-text="lang==='ar'?'لماذا ميزان':'Why Mizan'"></a>
            <a href="{{ route('pricing') }}" @click="open=false" class="block py-2 px-3 text-gray-200 hover:text-gold rounded-lg" x-text="lang==='ar'?'الأسعار':'Pricing'"></a>
            <a href="{{ route('home') }}#contact" @click="open=false" class="block py-2 px-3 text-gray-200 hover:text-gold rounded-lg" x-text="lang==='ar'?'تواصل معنا':'Contact'"></a>
            <div class="flex items-center gap-3 pt-3 border-t border-white/10">
                <button @click="toggleLang()" class="text-xs font-medium border border-gray-500 text-gray-200 rounded-full px-3 py-1" x-text="lang==='ar'?'EN':'ع'"></button>
                <button @click="toggleDark()" class="text-gray-200 hover:text-gold p-1">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3"/></svg>
                </button>
                <a href="/admin/login" class="text-sm text-gray-200 hover:text-gold px-2" x-text="lang==='ar'?'دخول':'Login'"></a>
                <a href="{{ route('register.plans') }}" class="flex-1 text-center text-sm font-medium py-2 rounded-lg bg-gold text-white" x-text="lang==='ar'?'ابدأ مجاناً':'Start Free'"></a>
            </div>
        </div>
    </div>
</nav>

<main class="pt-16 lg:pt-18">
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-navy-dark dark:bg-gray-900 text-gray-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            <!-- Brand -->
            <div>
                <div class="flex items-center gap-2 mb-4">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="logo" class="h-8 w-auto object-contain">
                    @else
                        <div class="w-9 h-9 rounded-full bg-gold flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#1E3A5F" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><line x1="12" y1="3" x2="12" y2="21"/><path d="M5 21h14"/><path d="M5 8l-3 5h6L5 8z"/><path d="M19 8l-3 5h6l-3-5z"/><line x1="5" y1="8" x2="19" y2="8"/></svg>
                        </div>
                    @endif
                    <span class="text-white font-bold text-xl" x-text="lang==='ar'?'{{ $brandAr }}':'{{ $brandEn }}'"></span>
                </div>
                <p class="text-sm leading-relaxed" x-text="lang==='ar'?'منصة متكاملة لإدارة مكاتب المحاماة: القضايا، الجلسات، العملاء، الفواتير والوثائق — مع ذكاء اصطناعي وبوابة عملاء.':'A complete platform for law offices: cases, hearings, clients, invoices, and documents — with AI and a client portal.'"></p>
            </div>

            <!-- Links -->
            <div>
                <h4 class="text-white font-bold mb-4" x-text="lang==='ar'?'روابط سريعة':'Quick Links'"></h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}#features" class="hover:text-gold transition-colors" x-text="lang==='ar'?'المميزات':'Features'"></a></li>
                    <li><a href="{{ route('home') }}#why-us" class="hover:text-gold transition-colors" x-text="lang==='ar'?'لماذا ميزان':'Why Mizan'"></a></li>
                    <li><a href="{{ route('pricing') }}" class="hover:text-gold transition-colors" x-text="lang==='ar'?'الأسعار':'Pricing'"></a></li>
                    <li><a href="{{ route('register.plans') }}" class="hover:text-gold transition-colors" x-text="lang==='ar'?'ابدأ مجاناً':'Start Free'"></a></li>
                    <li><a href="/admin/login" class="hover:text-gold transition-colors" x-text="lang==='ar'?'تسجيل الدخول':'Login'"></a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="text-white font-bold mb-4" x-text="lang==='ar'?'تواصل معنا':'Contact'"></h4>
                <ul class="space-y-3 text-sm">
                    @if(!empty($contact['phone']))
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11 11 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg><span dir="ltr">{{ $contact['phone'] }}</span></li>
                    @endif
                    @if(!empty($contact['email']))
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><a href="mailto:{{ $contact['email'] }}" class="hover:text-gold" dir="ltr">{{ $contact['email'] }}</a></li>
                    @endif
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span x-text="lang==='ar'?'{{ $contact['address_ar'] ?? '' }}':'{{ $contact['address_en'] ?? '' }}'"></span></li>
                </ul>
            </div>

            <!-- Social -->
            <div>
                <h4 class="text-white font-bold mb-4" x-text="lang==='ar'?'تابعنا':'Follow Us'"></h4>
                <div class="flex items-center gap-3 flex-wrap">
                    @foreach(['facebook'=>'M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z','instagram'=>'M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.43.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.43.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 01-1.38-.9 3.7 3.7 0 01-.9-1.38c-.16-.43-.36-1.06-.41-2.23C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.43-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16M12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.9.33 4.14.63a5.9 5.9 0 00-2.13 1.38A5.9 5.9 0 00.63 4.14C.33 4.9.13 5.78.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.06 1.27.26 2.15.56 2.91a5.9 5.9 0 001.38 2.13 5.9 5.9 0 002.13 1.38c.76.3 1.64.5 2.91.56C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c1.27-.06 2.15-.26 2.91-.56a5.9 5.9 0 002.13-1.38 5.9 5.9 0 001.38-2.13c.3-.76.5-1.64.56-2.91.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.06-1.27-.26-2.15-.56-2.91a5.9 5.9 0 00-1.38-2.13A5.9 5.9 0 0019.86.63c-.76-.3-1.64-.5-2.91-.56C15.67.01 15.26 0 12 0zm0 5.84a6.16 6.16 0 100 12.32 6.16 6.16 0 000-12.32zM12 16a4 4 0 110-8 4 4 0 010 8zm6.41-10.85a1.44 1.44 0 100 2.88 1.44 1.44 0 000-2.88z','linkedin'=>'M20.45 20.45h-3.56v-5.57c0-1.33-.03-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 110-4.13 2.06 2.06 0 010 4.13zm1.78 13.02H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z','twitter_x'=>'M18.24 2.25h3.31l-7.23 8.26 8.5 11.24h-6.66l-5.21-6.82-5.96 6.82H1.68l7.73-8.83L1.25 2.25H8.08l4.71 6.23 5.45-6.23zm-1.16 17.52h1.83L7.08 4.13H5.12z'] as $key => $path)
                        @if(!empty($contact[$key]))
                            <a href="{{ $contact[$key] }}" target="_blank" class="w-9 h-9 rounded-full bg-white/10 hover:bg-gold flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $path }}"/></svg>
                            </a>
                        @endif
                    @endforeach
                    @if(!empty($contact['whatsapp']))
                        <a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" class="w-9 h-9 rounded-full bg-white/10 hover:bg-green-500 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.82 11.82 0 018.413 3.488 11.82 11.82 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884a9.86 9.86 0 001.51 5.26l-.999 3.648 3.978-1.115z"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="border-t border-white/10 mt-10 pt-6 text-center text-sm">
            © {{ date('Y') }} <span x-text="lang==='ar'?'{{ $brandAr }}':'{{ $brandEn }}'"></span>. <span x-text="lang==='ar'?'جميع الحقوق محفوظة.':'All rights reserved.'"></span>
        </div>
    </div>
</footer>

</body>
</html>
