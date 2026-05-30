@php
$contact = $settings['contact'];
$logoPath = $settings['branding']['logo_path'] ?? null;
$logoUrl  = $logoPath
    ? (str_starts_with($logoPath, 'http') ? $logoPath : asset('storage/' . $logoPath))
    : null;

$socialLinks = [
    'facebook'  => ['url' => $contact['facebook']  ?? null, 'path' => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z'],
    'twitter_x' => ['url' => $contact['twitter_x'] ?? null, 'path' => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z'],
    'instagram' => ['url' => $contact['instagram'] ?? null, 'path' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z'],
    'linkedin'  => ['url' => $contact['linkedin']  ?? null, 'path' => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z'],
    'youtube'   => ['url' => $contact['youtube']   ?? null, 'path' => 'M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z'],
    'tiktok'    => ['url' => $contact['tiktok']    ?? null, 'path' => 'M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z'],
];
$activeSocials = array_filter($socialLinks, fn($s) => !empty($s['url']));
@endphp

<footer class="bg-navy-dark dark:bg-gray-950 text-gray-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Main footer content -->
        <div class="py-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

            <!-- Brand column -->
            <div class="lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-5">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="logo" class="h-9 w-auto object-contain">
                    @else
                        <div class="w-9 h-9 rounded-full bg-gold flex items-center justify-center">
                            <span class="text-navy font-bold text-lg" style="font-family:'Tajawal',sans-serif;">ع</span>
                        </div>
                    @endif
                    <span class="font-bold text-xl text-white" x-text="lang==='ar'?'عامر':'Amer'"></span>
                </a>
                <p class="text-sm leading-relaxed mb-6"
                    x-text="lang==='ar'
                        ? 'مكتب عامر للمحاماة — خدمات قانونية متكاملة بأعلى مستوى من الاحترافية والثقة منذ {{ $settings['hero']['founded_year'] }}'
                        : 'Amer Law Office — comprehensive legal services with the highest standards of professionalism and trust since {{ $settings['hero']['founded_year'] }}'">
                </p>
                <!-- Social links -->
                @if(count($activeSocials) > 0)
                <div class="flex gap-3 flex-wrap">
                    @foreach($activeSocials as $platform => $social)
                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener"
                       class="w-8 h-8 rounded-full bg-white/5 hover:bg-gold/20 hover:text-gold flex items-center justify-center transition-colors text-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="{{ $social['path'] }}"/>
                        </svg>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Quick links -->
            <div>
                <h4 class="text-white font-semibold mb-5" x-text="lang==='ar'?'روابط سريعة':'Quick Links'"></h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="#services"     class="hover:text-gold transition-colors" x-text="lang==='ar'?'خدماتنا':'Services'"></a></li>
                    <li><a href="#why-us"       class="hover:text-gold transition-colors" x-text="lang==='ar'?'لماذا نحن':'Why Us'"></a></li>
                    <li><a href="#team"         class="hover:text-gold transition-colors" x-text="lang==='ar'?'فريقنا':'Our Team'"></a></li>
                    <li><a href="#testimonials" class="hover:text-gold transition-colors" x-text="lang==='ar'?'آراء العملاء':'Testimonials'"></a></li>
                    <li><a href="#contact"      class="hover:text-gold transition-colors" x-text="lang==='ar'?'تواصل معنا':'Contact Us'"></a></li>
                </ul>
            </div>

            <!-- Services -->
            <div>
                <h4 class="text-white font-semibold mb-5" x-text="lang==='ar'?'خدماتنا':'Services'"></h4>
                <ul class="space-y-3 text-sm">
                    @foreach(array_slice($settings['services'], 0, 5) as $service)
                    <li><a href="#services" class="hover:text-gold transition-colors"
                           x-text="lang==='ar' ? '{{ $service['title_ar'] }}' : '{{ $service['title_en'] }}'"></a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Contact info -->
            <div>
                <h4 class="text-white font-semibold mb-5" x-text="lang==='ar'?'معلومات التواصل':'Contact'"></h4>
                <ul class="space-y-3 text-sm">
                    @if(!empty($contact['address_ar']))
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gold mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span x-text="lang==='ar' ? '{{ addslashes($contact['address_ar']) }}' : '{{ addslashes($contact['address_en'] ?? $contact['address_ar']) }}'"></span>
                    </li>
                    @endif
                    @if(!empty($contact['phone']))
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <a href="tel:{{ $contact['phone'] }}" class="hover:text-gold transition-colors" dir="ltr">{{ $contact['phone'] }}</a>
                    </li>
                    @endif
                    @if(!empty($contact['phone2']))
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <a href="tel:{{ $contact['phone2'] }}" class="hover:text-gold transition-colors" dir="ltr">{{ $contact['phone2'] }}</a>
                    </li>
                    @endif
                    @if(!empty($contact['email']))
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <a href="mailto:{{ $contact['email'] }}" class="hover:text-gold transition-colors" dir="ltr">{{ $contact['email'] }}</a>
                    </li>
                    @endif
                </ul>
                <!-- Portal link -->
                <div class="mt-6">
                    <a href="{{ route('portal.login') }}" class="text-xs px-3 py-2 rounded-lg bg-gold/20 hover:bg-gold text-gold hover:text-white text-center transition-colors block" x-text="lang==='ar'?'بوابة العميل':'Client Portal'"></a>
                </div>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="border-t border-white/10 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
            <p x-text="lang==='ar' ? '© ' + new Date().getFullYear() + ' مكتب عامر للمحاماة. جميع الحقوق محفوظة.' : '© ' + new Date().getFullYear() + ' Amer Law Office. All rights reserved.'"></p>
            <div class="flex gap-4">
                <a href="#" class="hover:text-gold transition-colors" x-text="lang==='ar'?'سياسة الخصوصية':'Privacy Policy'"></a>
                <a href="#" class="hover:text-gold transition-colors" x-text="lang==='ar'?'شروط الاستخدام':'Terms of Service'"></a>
            </div>
        </div>
    </div>
</footer>
