/**
 * MediCare Hospital — premium interactions
 * Scroll reveals + animated counters, safe under Livewire navigate.
 */

// Flag that JS is active so `.reveal` elements can be safely hidden by CSS.
document.documentElement.classList.add('js');

function initReveals() {
    const els = document.querySelectorAll('.reveal:not(.in)');
    if (!('IntersectionObserver' in window)) {
        els.forEach((el) => el.classList.add('in'));
        return;
    }
    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in');
                    io.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );
    els.forEach((el) => io.observe(el));
}

function animateCounter(el) {
    const target = parseInt(el.dataset.target || '0', 10) || 0;
    const suffix = el.dataset.suffix || '';
    const duration = 1600;
    const fmt = new Intl.NumberFormat('en-US');
    const start = performance.now();
    const step = (now) => {
        const p = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - p, 3); // ease-out cubic
        el.textContent = fmt.format(Math.round(target * eased)) + suffix;
        if (p < 1) requestAnimationFrame(step);
        else el.textContent = fmt.format(target) + suffix;
    };
    requestAnimationFrame(step);
}

// Header shadow on scroll — single global listener, safe across Livewire navigations.
let headerScrollBound = false;
function refreshHeaderScroll() {
    document.querySelectorAll('.site-header').forEach((el) => {
        el.classList.toggle('scrolled', window.scrollY > 8);
    });
}
function initHeaderScroll() {
    refreshHeaderScroll();
    if (headerScrollBound) return;
    headerScrollBound = true;
    window.addEventListener('scroll', refreshHeaderScroll, { passive: true });
}

function initCounters() {
    const counters = document.querySelectorAll('[data-count]');
    if (!counters.length) return;
    if (!('IntersectionObserver' in window)) {
        counters.forEach((el) => {
            el.textContent =
                new Intl.NumberFormat('en-US').format(parseInt(el.dataset.target || '0', 10)) +
                (el.dataset.suffix || '');
        });
        return;
    }
    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    io.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.5 }
    );
    counters.forEach((el) => io.observe(el));
}

// Deferred module — DOM is already parsed, so init immediately (no flash gap,
// no duplicate counter loops). Livewire navigations re-init via bindLivewireReinit.
initReveals();
initCounters();
initHeaderScroll();

// Livewire navigate / morph re-runs (register once, avoid duplicates)
function bindLivewireReinit() {
    document.addEventListener('livewire:navigated', () => {
        initReveals();
        initCounters();
        initHeaderScroll();
    });
}
document.addEventListener('livewire:init', bindLivewireReinit);
