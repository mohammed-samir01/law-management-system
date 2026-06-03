<nav
    x-data="{ scrolled: false, open: false }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
    :class="scrolled ? 'bg-navy/95 shadow-lg backdrop-blur-md' : 'bg-navy/70 backdrop-blur-sm'"
    class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-20">

            <!-- Logo -->
            @php
                $_navLogoPath = $settings['branding']['logo_path'] ?? null;
                $_navLogoUrl  = $_navLogoPath
                    ? (str_starts_with($_navLogoPath, 'http') ? $_navLogoPath : asset('storage/' . $_navLogoPath))
                    : null;
            @endphp
            <a href="#hero" class="flex items-center gap-2 flex-shrink-0">
                @if($_navLogoUrl)
                    <img src="{{ $_navLogoUrl }}" alt="logo" class="h-9 w-auto object-contain">
                @else
                    <div class="w-10 h-10 rounded-full bg-gold flex items-center justify-center shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#1E3A5F" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                            <line x1="12" y1="3" x2="12" y2="21"/>
                            <path d="M5 21h14"/>
                            <path d="M5 8l-3 5h6L5 8z"/>
                            <path d="M19 8l-3 5h6l-3-5z"/>
                            <line x1="5" y1="8" x2="19" y2="8"/>
                        </svg>
                    </div>
                @endif
                <span
                    class="text-white font-bold text-xl transition-colors duration-300"
                    x-text="lang === 'ar' ? (window.officeName?.ar || 'المكتب') : (window.officeName?.en || 'Office')"
                ></span>
            </a>

            <!-- Desktop nav links -->
            <div class="hidden lg:flex items-center gap-8">
                <template x-if="lang === 'ar'">
                    <div class="flex items-center gap-8">
                        <a href="#hero" class="text-sm font-medium text-gold transition-colors">الرئيسية</a>
                        <a href="#services"      class="text-sm font-medium text-gray-200 hover:text-gold transition-colors">خدماتنا</a>
                        <a href="#why-us"        class="text-sm font-medium text-gray-200 hover:text-gold transition-colors" x-text="'لماذا ' + (window.officeName?.ar || 'نحن')"></a>
                        <a href="#team"          class="text-sm font-medium text-gray-200 hover:text-gold transition-colors">فريقنا</a>
                        <a href="#testimonials"  class="text-sm font-medium text-gray-200 hover:text-gold transition-colors">آراء العملاء</a>
                        <a href="#contact"       class="text-sm font-medium text-gray-200 hover:text-gold transition-colors">تواصل معنا</a>
                    </div>
                </template>
                <template x-if="lang === 'en'">
                    <div class="flex items-center gap-8">
                        <a href="{{ route('home') }}" class="text-sm font-medium text-gold transition-colors">Home</a>
                        <a href="#services"     class="text-sm font-medium text-gray-200 hover:text-gold transition-colors">Services</a>
                        <a href="#why-us"       class="text-sm font-medium text-gray-200 hover:text-gold transition-colors">Why Us</a>
                        <a href="#team"         class="text-sm font-medium text-gray-200 hover:text-gold transition-colors">Team</a>
                        <a href="#testimonials" class="text-sm font-medium text-gray-200 hover:text-gold transition-colors">Testimonials</a>
                        <a href="#contact"      class="text-sm font-medium text-gray-200 hover:text-gold transition-colors">Contact</a>
                    </div>
                </template>
            </div>

            <!-- Right actions -->
            <div class="hidden lg:flex items-center gap-3">
                <!-- Lang toggle -->
                <button
                    @click="toggleLang()"
                    class="text-xs font-medium border border-gray-400 text-gray-200 hover:border-gold hover:text-gold rounded-full px-3 py-1 transition-colors"
                    x-text="lang === 'ar' ? 'EN' : 'ع'"
                ></button>

                <!-- Dark mode toggle -->
                <button
                    @click="toggleDark()"
                    class="p-1.5 rounded-full text-gray-200 hover:text-gold transition-colors"
                >
                    <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

                <!-- Client portal -->
                <a
                    href="{{ route('portal.login') }}"
                    class="text-sm font-medium px-4 py-2 rounded-lg bg-gold hover:bg-gold-dark text-white transition-colors"
                    x-text="lang === 'ar' ? 'بوابة العميل' : 'Client Portal'"
                ></a>

            </div>

            <!-- Mobile menu button -->
            <button
                @click="open = !open"
                class="lg:hidden p-2 rounded-lg text-gray-200 transition-colors"
            >
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="lg:hidden bg-navy/97 backdrop-blur-md border-t border-white/10"
    >
        <div class="px-4 py-4 space-y-2">
            <a href="#hero" @click="open=false" class="block py-2 px-3 text-gold hover:bg-white/5 rounded-lg transition-colors font-medium" x-text="lang==='ar'?'الرئيسية':'Home'"></a>
            <a href="#services"     @click="open=false" class="block py-2 px-3 text-gray-200 hover:text-gold hover:bg-white/5 rounded-lg transition-colors" x-text="lang==='ar'?'خدماتنا':'Services'"></a>
            <a href="#why-us"       @click="open=false" class="block py-2 px-3 text-gray-200 hover:text-gold hover:bg-white/5 rounded-lg transition-colors" x-text="lang==='ar' ? 'لماذا ' + (window.officeName?.ar || 'نحن') : 'Why Us'"></a>
            <a href="#team"         @click="open=false" class="block py-2 px-3 text-gray-200 hover:text-gold hover:bg-white/5 rounded-lg transition-colors" x-text="lang==='ar'?'فريقنا':'Team'"></a>
            <a href="#testimonials" @click="open=false" class="block py-2 px-3 text-gray-200 hover:text-gold hover:bg-white/5 rounded-lg transition-colors" x-text="lang==='ar'?'آراء العملاء':'Testimonials'"></a>
            <a href="#contact"      @click="open=false" class="block py-2 px-3 text-gray-200 hover:text-gold hover:bg-white/5 rounded-lg transition-colors" x-text="lang==='ar'?'تواصل معنا':'Contact'"></a>
            <div class="flex items-center gap-3 pt-3 border-t border-white/10">
                <button @click="toggleLang()" class="text-xs font-medium border border-gray-500 text-gray-200 hover:border-gold hover:text-gold rounded-full px-3 py-1 transition-colors" x-text="lang==='ar'?'EN':'ع'"></button>
                <button @click="toggleDark()" class="text-gray-200 hover:text-gold transition-colors p-1">
                    <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                <a href="{{ route('portal.login') }}" class="flex-1 text-center text-sm font-medium py-2 rounded-lg bg-gold hover:bg-gold-dark text-white transition-colors" x-text="lang==='ar'?'بوابة العميل':'Client Portal'"></a>
            </div>
        </div>
    </div>
</nav>
