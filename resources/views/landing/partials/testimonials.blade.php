@php
$testimonials = $settings['testimonials'];
@endphp

<section id="testimonials" class="py-24 bg-navy dark:bg-gray-900 relative overflow-hidden">

    <!-- Subtle background pattern -->
    <div class="absolute inset-0 opacity-5 pointer-events-none" aria-hidden="true">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="testi-hex" x="0" y="0" width="60" height="60" patternUnits="userSpaceOnUse">
                    <polygon points="30,3 57,18 57,42 30,57 3,42 3,18"
                             fill="none" stroke="#C9A84C" stroke-width="0.5"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#testi-hex)"/>
        </svg>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section header -->
        <div class="text-center mb-16 scroll-animate opacity-0">
            <span class="inline-flex items-center gap-2 text-gold font-semibold text-sm uppercase tracking-widest mb-4">
                <span class="w-8 h-px bg-gold inline-block"></span>
                <span x-text="lang==='ar'?'شهاداتهم':'Testimonials'"></span>
                <span class="w-8 h-px bg-gold inline-block"></span>
            </span>
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4"
                style="font-family:'Playfair Display',serif;"
                x-text="lang==='ar'?'ماذا يقول عملاؤنا':'What Our Clients Say'"></h2>
            <p class="text-white/50 max-w-lg mx-auto text-sm leading-relaxed"
               x-text="lang==='ar'?'ثقة عملائنا هي أهم شهادة على جودة خدماتنا القانونية':'Our clients\' trust is the greatest testament to our legal excellence'"></p>
        </div>

        <!-- Testimonials grid -->
        @if(count($testimonials) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">

            @foreach($testimonials as $i => $t)
            <div class="group flex flex-col scroll-animate opacity-0"
                 style="animation-delay:{{ $i * 0.1 }}s;">

                <div class="flex flex-col flex-1 relative bg-white/5 hover:bg-white/10
                            border border-white/10 hover:border-gold/40
                            rounded-2xl p-7 sm:p-8
                            transition-all duration-400 hover:-translate-y-1
                            backdrop-blur-sm">

                    <!-- Top row: quote mark + stars -->
                    <div class="flex items-start justify-between mb-5">
                        <!-- Giant decorative quote -->
                        <div class="text-gold/60 leading-none select-none"
                             style="font-size:5rem; font-family:'Playfair Display',serif; line-height:1;">
                            "
                        </div>
                        <!-- Stars -->
                        <div class="flex gap-0.5 text-gold mt-2">
                            @for($s = 0; $s < ($t['rating'] ?? 5); $s++)
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @endfor
                        </div>
                    </div>

                    <!-- Quote text -->
                    <p class="text-white/80 leading-relaxed text-sm sm:text-base flex-1 mb-7"
                       x-text="lang==='ar' ? '{{ addslashes($t['quote_ar']) }}' : '{{ addslashes($t['quote_en']) }}'"></p>

                    <!-- Divider -->
                    <div class="w-full h-px bg-white/10 mb-6"></div>

                    <!-- Client info -->
                    <div class="flex items-center gap-4">
                        <!-- Avatar -->
                        <div class="w-11 h-11 rounded-full bg-gold/20 border border-gold/40
                                    flex items-center justify-center flex-shrink-0">
                            <span class="text-gold font-bold text-base"
                                  style="font-family:'Tajawal',sans-serif;">
                                {{ $t['initials'] ?? mb_substr($t['name_ar'] ?? '؟', 0, 1) }}
                            </span>
                        </div>
                        <div class="min-w-0">
                            <div class="font-semibold text-white text-sm leading-tight"
                                 x-text="lang==='ar' ? '{{ $t['name_ar'] }}' : '{{ $t['name_en'] }}'"></div>
                            <div class="text-white/45 text-xs mt-0.5 truncate"
                                 x-text="lang==='ar' ? '{{ $t['role_ar'] }}' : '{{ $t['role_en'] }}'"></div>
                        </div>
                        <!-- Verified badge -->
                        <div class="ms-auto flex-shrink-0">
                            <div class="inline-flex items-center gap-1 bg-gold/15 border border-gold/30 rounded-full px-2.5 py-1">
                                <svg class="w-3 h-3 text-gold" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gold text-xs font-medium"
                                      x-text="lang==='ar'?'موثّق':'Verified'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
        @endif

    </div>
</section>
