@php
    $hero = $settings['hero'];

    $heroImagePath = $hero['image_path'] ?? null;
    $heroImageUrl  = match(true) {
        !$heroImagePath                              => null,
        str_starts_with($heroImagePath, 'http')      => $heroImagePath,
        str_starts_with($heroImagePath, '/')         => asset(ltrim($heroImagePath, '/')),
        default                                      => asset('storage/' . $heroImagePath),
    };

    $logoPath = $settings['branding']['logo_path'] ?? null;
    $logoUrl  = match(true) {
        !$logoPath                              => null,
        str_starts_with($logoPath, 'http')      => $logoPath,
        str_starts_with($logoPath, '/')         => asset(ltrim($logoPath, '/')),
        default                                 => asset('storage/' . $logoPath),
    };
@endphp

<section
    id="hero"
    class="relative min-h-screen flex items-center overflow-hidden bg-navy dark:bg-navy-dark"
    data-parallax-section
>
    <!-- Geometric SVG background pattern -->
    <div class="absolute inset-0 opacity-10" aria-hidden="true">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="geo" x="0" y="0" width="80" height="80" patternUnits="userSpaceOnUse">
                    <polygon points="40,5 75,25 75,55 40,75 5,55 5,25" fill="none" stroke="#C9A84C" stroke-width="0.5"/>
                    <circle cx="40" cy="40" r="3" fill="#C9A84C" opacity="0.4"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#geo)"/>
        </svg>
    </div>

    <!-- Gradient overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-navy-dark/80 via-navy/50 to-navy-light/30" aria-hidden="true"></div>

    <!-- Floating circles decoration -->
    <div class="absolute top-20 end-10 w-64 h-64 rounded-full border border-gold/20 animate-pulse" aria-hidden="true"></div>
    <div class="absolute bottom-20 start-10 w-40 h-40 rounded-full border border-gold/10" aria-hidden="true"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-28 lg:py-32 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">

            <!-- Text content -->
            <div class="order-2 lg:order-1">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 bg-gold/20 border border-gold/40 rounded-full px-4 py-1.5 mb-6 scroll-animate opacity-0">
                    <span class="w-2 h-2 rounded-full bg-gold animate-pulse"></span>
                    <span class="text-gold text-sm font-medium"
                        x-text="lang==='ar'
                            ? 'مكتب محاماة متخصص منذ {{ $hero['founded_year'] }}'
                            : 'Specialized Law Firm Since {{ $hero['founded_year'] }}'">
                    </span>
                </div>

                <!-- Typing headline -->
                <h1
                    id="hero-typing"
                    class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 scroll-animate opacity-0"
                    style="font-family:'Playfair Display',serif; animation-delay:0.1s;"
                    x-text="lang==='ar' ? '' : ''"
                    data-text-ar="{{ $hero['heading_ar'] }}"
                    data-text-en="{{ $hero['heading_en'] }}"
                ></h1>

                <p
                    class="text-lg text-gray-300 leading-relaxed mb-8 max-w-lg scroll-animate opacity-0"
                    style="animation-delay:0.2s;"
                    x-text="lang==='ar' ? '{{ addslashes($hero['subtitle_ar']) }}' : '{{ addslashes($hero['subtitle_en']) }}'"
                ></p>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-4 scroll-animate opacity-0" style="animation-delay:0.3s;">
                    <a
                        href="#contact"
                        class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-gold hover:bg-gold-dark text-white font-semibold text-sm transition-all duration-300 shadow-lg shadow-gold/30 hover:shadow-gold/50 hover:-translate-y-0.5"
                        x-text="lang==='ar' ? 'استشارة مجانية' : 'Free Consultation'"
                    ></a>
                    <a
                        href="#services"
                        class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl border border-white/30 hover:border-gold text-white hover:text-gold font-semibold text-sm transition-all duration-300 hover:-translate-y-0.5"
                        x-text="lang==='ar' ? 'تعرف على خدماتنا' : 'Our Services'"
                    ></a>
                </div>
            </div>

            <!-- Hero visual -->
            <div class="order-1 lg:order-2 flex justify-center items-center scroll-animate opacity-0" style="animation-delay:0.2s;">

                <style>
                    /* Mobile: hide rings, shrink card */
                    @media (max-width: 639px) {
                        .hero-rings { display: none; }
                        .hero-card-wrap { width: 220px !important; height: 260px !important; }
                        .hero-card-wrap::before {
                            content: '';
                            position: absolute;
                            inset: -20px;
                            border-radius: 50%;
                            background: radial-gradient(circle, rgba(201,168,76,0.15) 0%, transparent 70%);
                            animation: glow-pulse 4s ease-in-out infinite;
                        }
                    }
                    /* Tablet */
                    @media (min-width: 640px) and (max-width: 1023px) {
                        .hero-card-wrap { width: 270px !important; height: 320px !important; }
                    }
                    /* Animated border spinner */
                    .hero-border-spinner {
                        position: absolute;
                        inset: -60%;
                        background: conic-gradient(
                            from 0deg,
                            transparent 0deg,
                            rgba(201,168,76,0.9) 40deg,
                            rgba(201,168,76,0.4) 80deg,
                            transparent 130deg,
                            transparent 220deg,
                            rgba(201,168,76,0.7) 260deg,
                            rgba(201,168,76,0.3) 300deg,
                            transparent 340deg
                        );
                        animation: shimmer-border 5s linear infinite;
                    }
                </style>

                <!--
                    Rounded rectangle card with:
                    - Animated conic-gradient border (shimmer effect)
                    - Picture-frame gold corner brackets
                    - Floating animation
                    - Circular orbiting rings around it (desktop only)
                -->
                <div class="relative hero-card-wrap flex-shrink-0"
                     style="width:360px; height:440px;
                            animation: float 7s ease-in-out infinite;">

                    <!-- ── RINGS (desktop only) ─────────────────────────── -->
                    <div class="hero-rings"
                         style="position:absolute; top:50%; left:50%;
                                width:148%; height:138%;
                                transform:translate(-50%,-50%);
                                pointer-events:none; overflow:visible;">

                        <!-- Glow -->
                        <div style="position:absolute; inset:0; border-radius:50%;
                                    background:radial-gradient(circle at center,rgba(201,168,76,0.14) 0%,transparent 65%);
                                    animation:glow-pulse 4s ease-in-out infinite;"></div>

                        <!-- Ring 1: outer dashed, clockwise -->
                        <div style="position:absolute; inset:-2%; border-radius:50%;
                                    border:1.5px dashed rgba(201,168,76,0.25);
                                    animation:spin-slow 34s linear infinite;"></div>

                        <!-- Orbiting dot 1: gold glowing -->
                        <div style="position:absolute; inset:-2%; border-radius:50%;
                                    animation:spin-slow 34s linear infinite;">
                            <div style="position:absolute;top:-7px;left:50%;transform:translateX(-50%);
                                        width:13px;height:13px;border-radius:50%;background:#C9A84C;
                                        box-shadow:0 0 12px 4px rgba(201,168,76,0.7);"></div>
                        </div>

                        <!-- Orbiting dot 2: white, 180° offset -->
                        <div style="position:absolute; inset:-2%; border-radius:50%;
                                    animation:spin-slow 34s linear infinite; animation-delay:-17s;">
                            <div style="position:absolute;top:-5px;left:50%;transform:translateX(-50%);
                                        width:8px;height:8px;border-radius:50%;
                                        background:rgba(255,255,255,0.5);"></div>
                        </div>

                        <!-- Ring 2: counter-clockwise -->
                        <div style="position:absolute; inset:8%; border-radius:50%;
                                    border:1px solid rgba(201,168,76,0.14);
                                    animation:spin-slow-reverse 24s linear infinite;"></div>

                        <!-- Orbiting dot 3: small gold -->
                        <div style="position:absolute; inset:8%; border-radius:50%;
                                    animation:spin-slow-reverse 24s linear infinite;">
                            <div style="position:absolute;top:-5px;left:50%;transform:translateX(-50%);
                                        width:9px;height:9px;border-radius:50%;
                                        background:rgba(201,168,76,0.8);
                                        box-shadow:0 0 7px 2px rgba(201,168,76,0.4);"></div>
                        </div>
                    </div>

                    <!-- ── CARD ──────────────────────────────────────────── -->

                    <!-- Glow behind card -->
                    <div style="position:absolute; inset:-8px; border-radius:32px;
                                background:rgba(201,168,76,0.12);
                                filter:blur(20px);
                                animation:glow-pulse 4s ease-in-out infinite;"></div>

                    <!-- Animated border wrapper (overflow:hidden clips the spinner) -->
                    <div style="position:absolute; inset:0; border-radius:24px;
                                overflow:hidden; padding:2px;
                                box-shadow:0 32px 80px rgba(0,0,0,0.50),
                                           0 0 60px rgba(201,168,76,0.12);">

                        <!-- Spinning conic-gradient — creates animated border -->
                        <div class="hero-border-spinner"></div>

                        <!-- Dark background to "fill" the card inside the 2px border -->
                        <div style="position:relative; width:100%; height:100%;
                                    border-radius:22px; overflow:hidden;
                                    background:rgba(18,38,65,0.95);">
                            @if($heroImageUrl)
                                <img src="{{ $heroImageUrl }}" alt="hero"
                                     style="width:100%;height:100%;object-fit:cover;
                                            object-position:center center;display:block;">
                            @elseif($logoUrl)
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                    <img src="{{ $logoUrl }}" alt="logo" style="width:55%;object-fit:contain;">
                                </div>
                            @else
                                <div style="width:100%;height:100%;display:flex;flex-direction:column;
                                            align-items:center;justify-content:center;gap:.75rem;padding:2rem;">
                                    <svg style="width:3rem;height:3rem;color:#C9A84C;"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                              d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                    </svg>
                                    <p style="color:#C9A84C;font-weight:700;font-size:1rem;font-family:'Tajawal',sans-serif;"
                                       x-text="lang==='ar'?'عامر':'Amer'"></p>
                                </div>
                            @endif
                            <!-- Shine -->
                            <div style="position:absolute;inset:0;pointer-events:none;
                                        background:linear-gradient(140deg,rgba(255,255,255,0.05) 0%,transparent 40%);"></div>
                        </div>
                    </div>

                    <!-- ── CORNER BRACKETS (picture-frame effect) ───────── -->
                    <div style="position:absolute;inset:-2px;pointer-events:none;">
                        <!-- Top-left -->
                        <div style="position:absolute;top:10px;left:10px;width:22px;height:22px;
                                    border-top:2.5px solid #C9A84C;border-left:2.5px solid #C9A84C;
                                    border-radius:4px 0 0 0;opacity:0.85;"></div>
                        <!-- Top-right -->
                        <div style="position:absolute;top:10px;right:10px;width:22px;height:22px;
                                    border-top:2.5px solid #C9A84C;border-right:2.5px solid #C9A84C;
                                    border-radius:0 4px 0 0;opacity:0.85;"></div>
                        <!-- Bottom-left -->
                        <div style="position:absolute;bottom:10px;left:10px;width:22px;height:22px;
                                    border-bottom:2.5px solid #C9A84C;border-left:2.5px solid #C9A84C;
                                    border-radius:0 0 0 4px;opacity:0.85;"></div>
                        <!-- Bottom-right -->
                        <div style="position:absolute;bottom:10px;right:10px;width:22px;height:22px;
                                    border-bottom:2.5px solid #C9A84C;border-right:2.5px solid #C9A84C;
                                    border-radius:0 0 4px 0;opacity:0.85;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats bar -->
        <div class="mt-12 sm:mt-16 lg:mt-20 grid grid-cols-3 scroll-animate opacity-0" style="animation-delay:0.4s;">
            <div class="text-center border-e border-white/10 px-4 py-2">
                <div class="text-5xl sm:text-6xl lg:text-7xl font-bold text-gold leading-none" data-counter="{{ $hero['stat_cases'] }}">0</div>
                <div class="text-gray-300 text-sm sm:text-base lg:text-lg mt-3 font-medium" x-text="lang==='ar'?'قضية ناجحة':'Successful Cases'"></div>
            </div>
            <div class="text-center border-e border-white/10 px-4 py-2">
                <div class="text-5xl sm:text-6xl lg:text-7xl font-bold text-gold leading-none" data-counter="{{ $hero['stat_years'] }}">0</div>
                <div class="text-gray-300 text-sm sm:text-base lg:text-lg mt-3 font-medium" x-text="lang==='ar'?'سنة خبرة':'Years Experience'"></div>
            </div>
            <div class="text-center px-4 py-2">
                <div class="text-5xl sm:text-6xl lg:text-7xl font-bold text-gold leading-none" data-counter="{{ $hero['stat_satisfaction'] }}">0</div>
                <div class="text-gray-300 text-sm sm:text-base lg:text-lg mt-3 font-medium" x-text="lang==='ar'?'رضا العملاء %':'Satisfaction %'"></div>
            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="absolute bottom-8 inset-x-0 flex justify-center animate-bounce">
        <a href="#services" class="text-gold/60 hover:text-gold transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </a>
    </div>
</section>
