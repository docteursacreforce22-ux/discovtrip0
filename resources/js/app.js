import './bootstrap';
import Alpine from 'alpinejs';

// ═══════════════════════════════════════════
// ALPINE.JS — chargé depuis npm (pas CDN)
// Font Awesome est chargé via CDN dans app.blade.php
// NE PAS importer ici pour éviter le double chargement
// ═══════════════════════════════════════════

window.Alpine = Alpine;
Alpine.start();

// NProgress — terminé au chargement (fallback si DOMContentLoaded tardif)
if (typeof NProgress !== 'undefined') {
    NProgress.done();
}

// ═══════════════════════════════════════════
// LOADER — masqué dès que la page est prête
// Délai réduit de 1700ms → 400ms
// Désactivé si prefers-reduced-motion
// ═══════════════════════════════════════════

window.addEventListener('load', () => {
    const loader = document.getElementById('dt-loader');
    if (!loader) return;

    const delay = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 400;
    setTimeout(() => loader.classList.add('out'), delay);
}, { once: true });

// ═══════════════════════════════════════════
// SCROLL REVEAL — IntersectionObserver
// ═══════════════════════════════════════════

const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('dt-visible');
            revealObserver.unobserve(entry.target);
        }
    });
}, {
    threshold: 0.08,
    rootMargin: '0px 0px -40px 0px',
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.dt-reveal').forEach(el => revealObserver.observe(el));
});

// ═══════════════════════════════════════════
// NAV SCROLL — classe scrolled sur la navbar
// ═══════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
    const nav = document.getElementById('dt-nav');
    if (!nav) return;

    if (window.scrollY > 40) nav.classList.add('dt-nav--scrolled');

    window.addEventListener('scroll', () => {
        nav.classList.toggle('dt-nav--scrolled', window.scrollY > 40);
    }, { passive: true });
});

// ═══════════════════════════════════════════
// WISHLIST — toggle favoris via fetch
// ═══════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-wishlist-id]');
        if (!btn || btn.dataset.loading) return;
        e.preventDefault();

        btn.dataset.loading = '1';
        const icon = btn.querySelector('i');

        // Feedback visuel immédiat
        btn.style.opacity = '0.6';

        try {
            const res = await fetch('/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    'Accept':        'application/json',
                },
                body: JSON.stringify({ offer_id: btn.dataset.wishlistId }),
                credentials: 'same-origin',
            });

            if (!res.ok) throw new Error('HTTP ' + res.status);

            const data = await res.json();

            if (icon) {
                icon.classList.toggle('fas', data.wishlisted);
                icon.classList.toggle('far', !data.wishlisted);
            }
            btn.setAttribute('aria-pressed', data.wishlisted ? 'true' : 'false');
            btn.classList.toggle('is-active', data.wishlisted);

            // Mettre à jour le badge nav wishlist
            const badge = document.querySelector('.dt-nav-wishlist-badge');
            if (badge && typeof data.count !== 'undefined') {
                badge.textContent = data.count;
                badge.style.display = data.count > 0 ? '' : 'none';
            }

        } catch (err) {
            if (err.message.includes('401') || err.message.includes('403')) {
                window.location.href = '/connexion';
            }
        } finally {
            btn.style.opacity = '';
            delete btn.dataset.loading;
        }
    });
});

// ═══════════════════════════════════════════
// MAGNETIC BUTTONS — effet hover premium
// Désactivé sur mobile (pointer: coarse)
// ═══════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
    if (window.matchMedia('(pointer: coarse)').matches) return;

    document.querySelectorAll('.dt-btn-copper, .dt-btn-outline').forEach(btn => {
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            const x = (e.clientX - rect.left - rect.width  / 2) * 0.2;
            const y = (e.clientY - rect.top  - rect.height / 2) * 0.2;
            btn.style.transform = `translate(${x}px, ${y}px)`;
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.transform = '';
        });
    });
});

// ═══════════════════════════════════════════
// UTILITAIRES GLOBAUX
// ═══════════════════════════════════════════

window.dtUtils = {
    observeReveal: () => {
        document.querySelectorAll('.dt-reveal:not(.dt-visible)').forEach(el => {
            revealObserver.observe(el);
        });
    },
};