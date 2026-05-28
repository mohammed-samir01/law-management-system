import Alpine from 'alpinejs';
window.Alpine = Alpine;

// Alpine global component — dark mode, lang, mobile menu
window.landingApp = () => ({
    darkMode: localStorage.getItem('darkMode') === 'true',
    lang: localStorage.getItem('lang') || 'ar',
    mobileMenu: false,

    toggleDark() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
    },

    toggleLang() {
        this.lang = this.lang === 'ar' ? 'en' : 'ar';
        localStorage.setItem('lang', this.lang);
    },
});

// ── Scroll animations (IntersectionObserver) ────────────────────────────────
function initScrollAnimations() {
    const els = document.querySelectorAll('.scroll-animate');
    if (!els.length) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-up');
                    entry.target.classList.remove('opacity-0');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15 }
    );

    els.forEach((el) => observer.observe(el));
}

// ── Counter animation ───────────────────────────────────────────────────────
function animateCounter(el, target, duration = 1800) {
    const start = performance.now();
    const update = (now) => {
        const elapsed = now - start;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
        el.textContent = Math.floor(eased * target).toLocaleString('ar-EG');
        if (progress < 1) requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
}

function initCounters() {
    const counters = document.querySelectorAll('[data-counter]');
    if (!counters.length) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const target = parseInt(entry.target.dataset.counter, 10);
                    animateCounter(entry.target, target);
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.5 }
    );

    counters.forEach((el) => observer.observe(el));
}

// ── Typing animation ────────────────────────────────────────────────────────
function initTypingAnimation() {
    const el = document.getElementById('hero-typing');
    if (!el) return;

    // Pick text based on current lang
    const getLang = () => document.documentElement.getAttribute('lang') || 'ar';
    const getText = () =>
        getLang() === 'ar'
            ? el.dataset.textAr || ''
            : el.dataset.textEn || '';

    let index = 0;
    let text = getText();
    el.textContent = '';

    function typeNext() {
        if (index < text.length) {
            el.textContent += text[index];
            index++;
            setTimeout(typeNext, 55);
        }
    }

    // Wait for hero section to be visible
    const heroSection = document.getElementById('hero');
    const observer = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting) {
                setTimeout(typeNext, 600);
                observer.disconnect();
            }
        },
        { threshold: 0.3 }
    );
    if (heroSection) observer.observe(heroSection);
    else setTimeout(typeNext, 800);

    // Re-type when lang changes
    document.documentElement.addEventListener('lang-changed', () => {
        text = getText();
        index = 0;
        el.textContent = '';
        typeNext();
    });
}

// ── Navbar scroll effect ────────────────────────────────────────────────────
function initNavbar() {
    // handled inline via Alpine x-init in navbar.blade.php
}

// ── Parallax ────────────────────────────────────────────────────────────────
function initParallax() {
    const section = document.querySelector('[data-parallax-section]');
    if (!section) return;

    const bg = section.querySelector('[data-parallax-section] > div:first-child');
    if (!bg) return;

    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        const rate = scrolled * 0.3;
        bg.style.transform = `translateY(${rate}px)`;
    }, { passive: true });
}

// ── CSS for scroll-animate ───────────────────────────────────────────────────
function injectAnimationCSS() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fadeUp 0.6s ease-out forwards;
        }
    `;
    document.head.appendChild(style);
}

// ── Bootstrap ────────────────────────────────────────────────────────────────
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    injectAnimationCSS();
    initScrollAnimations();
    initCounters();
    initTypingAnimation();
    initParallax();
});
