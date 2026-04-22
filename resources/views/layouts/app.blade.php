<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    {{-- ════════════════════════════════════════
         SÉCURITÉ
    ════════════════════════════════════════ --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token"                    content="{{ csrf_token() }}">
    <meta http-equiv="X-Frame-Options"         content="SAMEORIGIN">
    <meta http-equiv="X-Content-Type-Options"  content="nosniff">
    <meta name="referrer"                      content="strict-origin-when-cross-origin">

    {{-- ════════════════════════════════════════
         SEO — TITRES & DESCRIPTIONS
    ════════════════════════════════════════ --}}
    <title>@yield('title', 'DiscovTrip — L\'Afrique Authentique & Premium')</title>
    <meta name="description" content="@hasSection('description') @yield('description') @else Réservez des expériences uniques au Bénin. Culture, nature, gastronomie — découvrez l'Afrique autrement avec DiscovTrip. @endif">
    <meta name="robots"    content="@yield('robots', 'index, follow')">
    <link rel="canonical"  href="{{ url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:site_name"   content="DiscovTrip">
    <meta property="og:locale"      content="fr_FR">
    <meta property="og:title"       content="@yield('og_title',       'DiscovTrip — L\'Afrique Authentique')">
    <meta property="og:description" content="@yield('og_description', 'Expériences premium au Bénin')">
    <meta property="og:image"       content="@yield('og_image',       asset('images/og-default.jpg'))">

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="@yield('og_title',       'DiscovTrip — L\'Afrique Authentique')">
    <meta name="twitter:description" content="@yield('og_description', 'Expériences premium au Bénin')">
    <meta name="twitter:image"       content="@yield('og_image',       asset('images/og-default.jpg'))">

    {{-- Geo (SEO local Bénin) --}}
    <meta name="geo.region"   content="BJ">
    <meta name="geo.placename"content="Cotonou">

    {{-- Favicons --}}
    <link rel="icon"             href="{{ asset('favicon.ico') }}"                type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    {{-- ════════════════════════════════════════
         FONTS — Google Fonts avec preload
    ════════════════════════════════════════ --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style"
          href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,700&family=Syne:wght@400;500;600;700;800&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,700&family=Syne:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    {{-- Font Awesome 6 — chargé ici UNE SEULE FOIS pour toutes les pages
         Integrity retirée : elle peut bloquer le chargement si le hash ne correspond pas.
         Alternative propre : npm install @fortawesome/fontawesome-free + import dans app.js --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          referrerpolicy="no-referrer">

    {{-- Alpine.js — chargé via npm/Vite dans app.js (PAS de CDN pour éviter le double chargement) --}}

    {{-- ════════════════════════════════════════
         ASSETS VITE (CSS + JS compilés)
         NOTE : Tailwind CDN retiré — le design
         est 100% custom CSS via app.css / home.css
    ════════════════════════════════════════ --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Styles spécifiques à la page (home.css etc. via @push dans les vues) --}}
    @stack('styles')

    {{-- Balises meta supplémentaires (OG, canonical, JSON-LD) par page --}}
    @stack('meta')

    {{-- Schema.org JSON-LD global (SEO structuré — TravelAgency) --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "TravelAgency",
        "name": "DiscovTrip",
        "description": "Plateforme de réservation d'expériences touristiques authentiques au Bénin, Afrique de l'Ouest.",
        "url": "{{ config('app.url') }}",
        "logo": "{{ asset('images/logo.png') }}",
        "image": "{{ asset('images/og-default.jpg') }}",
        "telephone": "+22901000000",
        "email": "contact@discovtrip.com",
        "address": {
            "@@type": "PostalAddress",
            "streetAddress": "Haie Vive",
            "addressLocality": "Cotonou",
            "addressCountry": "BJ"
        },
        "areaServed": {
            "@@type": "Country",
            "name": "Bénin"
        },
        "priceRange": "5 000 – 100 000 FCFA",
        "sameAs": [
            "https://www.facebook.com/discovtrip",
            "https://www.instagram.com/discovtrip"
        ]
    }
    </script>

    {{-- JSON-LD spécifique à la page (offres, articles de blog, etc.) --}}
    @stack('jsonld')

    {{-- NProgress — barre de progression entre pages --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nprogress@0.2.0/nprogress.css">
    <style>
        #nprogress .bar { background: #D4A20F !important; height: 3px !important; }
        #nprogress .peg { box-shadow: 0 0 10px #D4A20F, 0 0 5px #D4A20F !important; }
        #nprogress .spinner-icon { border-top-color: #D4A20F !important; border-left-color: #D4A20F !important; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/nprogress@0.2.0/nprogress.min.js"></script>
    <script>
        NProgress.configure({ showSpinner: false, speed: 300, minimum: 0.08 });
        document.addEventListener('DOMContentLoaded', function() {
            // Démarrer à chaque clic sur un lien interne
            document.addEventListener('click', function(e) {
                var a = e.target.closest('a[href]');
                if (!a) return;
                var href = a.getAttribute('href');
                // Ignorer : ancres, externe, javascript, target=_blank, data-no-progress
                if (!href || href.startsWith('#') || href.startsWith('javascript') ||
                    a.target === '_blank' || a.hasAttribute('data-no-progress') ||
                    href.startsWith('mailto:') || href.startsWith('tel:') ||
                    (href.startsWith('http') && !href.startsWith(window.location.origin))) return;
                NProgress.start();
            });
            // Stopper quand la page est chargée
            window.addEventListener('pageshow', function() { NProgress.done(); });
        });
    </script>
</head>

{{-- ════════════════════════════════════════
     BODY — Alpine state global
════════════════════════════════════════ --}}
@php
    // Wishlist count — calculé UNE seule fois pour toute la page
    $navWishCount = auth()->check() ? (int) auth()->user()->wishlists()->count() : 0;
@endphp
<body
    class="antialiased"
    x-data="{
        navOpen:     false,
        navScrolled: false,
        wishCount:   {{ $navWishCount }},
    }"
    @scroll.window="navScrolled = (window.scrollY > 40)">

{{-- ────────────────────────────────────────
     SKIP LINK — accessibilité clavier
──────────────────────────────────────── --}}
<a href="#main-content" class="dt-skip-link">Aller au contenu principal</a>

{{-- ════════════════════════════════════════
     NAVIGATION PRINCIPALE
════════════════════════════════════════ --}}
<header role="banner">
<nav class="dt-nav"
     :class="{ 'dt-nav--scrolled': navScrolled }"
     aria-label="Navigation principale">

    <div class="dt-nav-inner">

        {{-- ── Logo ── --}}
        <a href="{{ route('home') }}"
           class="dt-logo"
           aria-label="DiscovTrip — Retour à l'accueil">
            <img src="{{ asset('images/logo.jpg') }}"
                 alt="DiscovTrip"
                 class="dt-logo-img"
                 width="44" height="44"
                 loading="eager">
            {{-- Texte masqué sur mobile (le logo image suffit) --}}
            <div class="dt-logo-text">
                <span class="dt-logo-name">DiscovTrip</span>
                <span class="dt-logo-tagline">Authentic Africa · Bénin</span>
            </div>
        </a>

        {{-- ── Liens desktop ── --}}
        <nav class="dt-nav-links" aria-label="Menu principal">
            <a href="{{ route('offers.index') }}"
               class="dt-nav-link {{ request()->routeIs('offers*') ? 'dt-nav-link--active' : '' }}">
                Expériences
            </a>
            <a href="{{ url('/destinations') }}"
               class="dt-nav-link {{ request()->is('destinations*') ? 'dt-nav-link--active' : '' }}">
                Destinations
            </a>
            <a href="{{ route('blog.index') }}"
               class="dt-nav-link {{ request()->routeIs('blog*') ? 'dt-nav-link--active' : '' }}">
                Blog
            </a>
            <a href="{{ url('/about') }}"
               class="dt-nav-link {{ request()->is('about*') ? 'dt-nav-link--active' : '' }}">
                À propos
            </a>
            <a href="{{ url('/contact') }}"
               class="dt-nav-link {{ request()->is('contact*') ? 'dt-nav-link--active' : '' }}">
                Contact
            </a>
        </nav>

        {{-- ── Auth + actions desktop ── --}}
        <div class="dt-nav-auth">

            @auth
            <a href="{{ route('account.wishlist') }}"
               class="dt-nav-wishlist"
               title="Mes favoris"
               aria-label="Mes favoris ({{ $navWishCount }})">
                <i class="fas fa-heart" aria-hidden="true"></i>
                @if($navWishCount > 0)
                    <span class="dt-nav-wishlist-badge" aria-hidden="true">
                        {{ $navWishCount }}
                    </span>
                @endif
            </a>
            @endauth

            @guest
                <a href="{{ route('login') }}"   class="dt-btn dt-btn--ghost-dark">Connexion</a>
                <a href="{{ route('register') }}" class="dt-btn dt-btn--ambre">
                    S'inscrire <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            @else
                <a href="{{ route('account.dashboard') }}" class="dt-nav-user">
                    <div class="dt-nav-avatar" aria-hidden="true">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="dt-nav-user-info">
                        <span class="dt-nav-user-name">{{ Str::limit(Auth::user()->name ?? '', 18) }}</span>
                        <span class="dt-nav-user-sub">Mon compte</span>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="dt-btn-icon" title="Se déconnecter" aria-label="Se déconnecter">
                        <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                    </button>
                </form>
            @endguest
        </div>

        {{-- ── Hamburger mobile ── --}}
        <button class="dt-hamburger"
                :class="{ 'dt-hamburger--open': navOpen }"
                @click="navOpen = !navOpen"
                :aria-expanded="navOpen.toString()"
                aria-controls="dt-mobile-menu"
                aria-label="Ouvrir le menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</nav>
</header>

{{-- ── Overlay mobile ── --}}
<div class="dt-mobile-overlay"
     :class="{ 'dt-mobile-overlay--open': navOpen }"
     x-cloak
     @click="navOpen = false"
     aria-hidden="true"></div>

{{-- ── Menu mobile ── --}}
<div id="dt-mobile-menu"
     class="dt-mobile-menu"
     :class="{ 'dt-mobile-menu--open': navOpen }"
     x-cloak
     :aria-hidden="(!navOpen).toString()"
     role="dialog"
     aria-label="Menu mobile">

    <nav aria-label="Menu mobile" class="dt-mobile-nav">
        @php
        $mobileLinks = [
            ['href' => route('offers.index'),  'label' => 'Expériences',  'icon' => 'fa-compass'],
            ['href' => url('/destinations'),    'label' => 'Destinations', 'icon' => 'fa-map-marked-alt'],
            ['href' => route('blog.index'),     'label' => 'Blog',         'icon' => 'fa-newspaper'],
            ['href' => url('/about'),           'label' => 'À propos',     'icon' => 'fa-info-circle'],
            ['href' => url('/contact'),         'label' => 'Contact',      'icon' => 'fa-envelope'],
        ];
        @endphp
        @foreach($mobileLinks as $link)
            <a href="{{ $link['href'] }}"
               class="dt-mobile-link"
               @click="navOpen = false">
                <i class="fas {{ $link['icon'] }} dt-mobile-link-icon" aria-hidden="true"></i>
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="dt-mobile-auth">
        @guest
            <a href="{{ route('login') }}"    class="dt-btn dt-btn--ghost-dark dt-btn--full">Connexion</a>
            <a href="{{ route('register') }}" class="dt-btn dt-btn--ambre    dt-btn--full">
                S'inscrire <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </a>
        @else
            <a href="{{ route('account.dashboard') }}" class="dt-mobile-user-card">
                <div class="dt-nav-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                <div>
                    <div class="dt-mobile-user-name">{{ Auth::user()->name }}</div>
                    <div class="dt-mobile-user-sub">Mon espace</div>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dt-btn dt-btn--ghost-danger dt-btn--full">
                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Déconnexion
                </button>
            </form>
        @endguest
    </div>
</div>

{{-- ════════════════════════════════════════
     FLASH MESSAGES (toast auto-dismiss)
════════════════════════════════════════ --}}
@if(session('success') || session('error') || session('info') || session('warning'))
<div class="dt-toast-stack"
     x-data="{ show: true }"
     x-show="show"
     x-cloak
     x-init="setTimeout(() => show = false, 6000)"
     x-transition:enter="dt-toast-enter"
     x-transition:enter-start="dt-toast-enter-start"
     x-transition:enter-end="dt-toast-enter-end"
     x-transition:leave="dt-toast-leave"
     x-transition:leave-start="dt-toast-leave-start"
     x-transition:leave-end="dt-toast-leave-end"
     aria-live="polite"
     aria-atomic="true">

    @php
    $toasts = [
        'success' => ['icon' => 'fa-check-circle',         'label' => 'Succès',      'type' => 'success'],
        'error'   => ['icon' => 'fa-times-circle',          'label' => 'Erreur',      'type' => 'error'],
        'info'    => ['icon' => 'fa-info-circle',           'label' => 'Information', 'type' => 'info'],
        'warning' => ['icon' => 'fa-exclamation-triangle',  'label' => 'Attention',   'type' => 'warning'],
    ];
    @endphp

    @foreach($toasts as $key => $cfg)
        @if(session($key))
        <div class="dt-toast dt-toast--{{ $cfg['type'] }}" role="alert">
            <i class="fas {{ $cfg['icon'] }} dt-toast-icon" aria-hidden="true"></i>
            <div class="dt-toast-body">
                <p class="dt-toast-title">{{ $cfg['label'] }}</p>
                <p class="dt-toast-msg">{{ session($key) }}</p>
            </div>
            <button @click="show = false" class="dt-toast-close" aria-label="Fermer">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
        @endif
    @endforeach
</div>
@endif

{{-- ════════════════════════════════════════
     CONTENU PRINCIPAL
════════════════════════════════════════ --}}
<main id="main-content" role="main" tabindex="-1" style="padding-top: var(--nav-h)">
    @yield('content')
</main>

{{-- ════════════════════════════════════════
     FOOTER
════════════════════════════════════════ --}}
<footer class="dt-footer" aria-label="Pied de page">

    <div class="dt-footer-bar" aria-hidden="true"></div>

    <div class="dt-footer-inner">
        <div class="dt-footer-grid">

            {{-- ── Brand ── --}}
            <div class="dt-footer-brand">
                <a href="{{ route('home') }}" class="dt-footer-logo" aria-label="DiscovTrip — Accueil">
                    <img src="{{ asset('images/logo.jpg') }}"
                         alt="DiscovTrip"
                         class="dt-footer-logo-img"
                         width="36" height="36"
                         loading="lazy">
                    <span class="dt-footer-logo-name">DiscovTrip</span>
                </a>
                <p class="dt-footer-brand-desc">
                    L'Afrique authentique et premium à portée de clic.
                    Expériences inoubliables au Bénin, conçues par des experts locaux.
                </p>
                <div class="dt-footer-socials" aria-label="Réseaux sociaux">
                    @foreach([
                        ['icon' => 'fa-instagram',   'href' => 'https://www.instagram.com/discovtrip', 'label' => 'Instagram'],
                        ['icon' => 'fa-facebook-f',  'href' => 'https://www.facebook.com/discovtrip', 'label' => 'Facebook'],
                        ['icon' => 'fa-whatsapp',   'href' =>  'https://wa.me/2290XXXXXXXXX',           'label' => 'WhatsApp'],
                        ['icon' => 'fa-twitter',     'href' => '#', 'label' => 'Twitter / X'],
                        ['icon' => 'fa-linkedin-in', 'href' => '#', 'label' => 'LinkedIn'],
                        ['icon' => 'fa-youtube',     'href' => '#', 'label' => 'YouTube'],
                    ] as $social)
                        <a href="{{ $social['href'] }}"
                           class="dt-footer-social"
                           aria-label="{{ $social['label'] }}"
                           rel="noopener noreferrer" target="_blank">
                            <i class="fab {{ $social['icon'] }}" aria-hidden="true"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ── Explorer ── --}}
            <div>
                <h3 class="dt-footer-heading">Explorer</h3>
                <ul>
                    <li><a href="{{ route('offers.index') }}" class="dt-footer-link">Toutes les expériences</a></li>
                    <li><a href="{{ url('/destinations') }}"  class="dt-footer-link">Destinations</a></li>
                    <li><a href="{{ route('blog.index') }}"   class="dt-footer-link">Blog & Conseils</a></li>
                    <li><a href="{{ url('/about') }}"         class="dt-footer-link">Notre histoire</a></li>
                    <li><a href="{{ url('/contact') }}"       class="dt-footer-link">Contactez-nous</a></li>
                </ul>
            </div>

            {{-- ── Pratique ── --}}
            <div>
                <h3 class="dt-footer-heading">Pratique</h3>
                <ul>
                    <li><a href="{{ route('account.dashboard') }}"                    class="dt-footer-link">Mon espace</a></li>
                    <li><a href="{{ route('account.bookings') }}"                     class="dt-footer-link">Mes réservations</a></li>
                    <li><a href="{{ route('account.wishlist') }}"                     class="dt-footer-link">Mes favoris</a></li>
                    <li><a href="{{ url('/conditions-utilisation') }}"                class="dt-footer-link">Conditions d'utilisation</a></li>
                    <li><a href="{{ url('/confidentialite') }}"                       class="dt-footer-link">Confidentialité</a></li>
                    <li><a href="{{ url('/cancellation') }}"                          class="dt-footer-link">Annulation gratuite</a></li>
                    <li><a href="{{ url('/faq') }}"                                   class="dt-footer-link">FAQ</a></li>
                </ul>
            </div>

            {{-- ── Contact ── --}}
            <div>
                <h3 class="dt-footer-heading">Contact</h3>
                <div class="dt-footer-contacts">
                    <div class="dt-footer-contact-row">
                        <div class="dt-footer-contact-ico dt-footer-contact-ico--ambre">
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="dt-footer-contact-label">Email</p>
                            <a href="mailto:contact@discovtrip.com" class="dt-footer-contact-value">
                                contact@discovtrip.com
                            </a>
                        </div>
                    </div>
                    <div class="dt-footer-contact-row">
                        <div class="dt-footer-contact-ico dt-footer-contact-ico--foret">
                            <i class="fas fa-phone" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="dt-footer-contact-label">Téléphone</p>
                            <a href="tel:+22901000000" class="dt-footer-contact-value">+229 01 00 00 00 00</a>
                        </div>
                    </div>
                    <div class="dt-footer-contact-row">
                        <div class="dt-footer-contact-ico dt-footer-contact-ico--braise">
                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="dt-footer-contact-label">Localisation</p>
                            <span class="dt-footer-contact-value">Cotonou, Bénin 🇧🇯</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Bottom bar ── --}}
        <div class="dt-footer-bottom">
            <p class="dt-footer-copy">
                &copy; {{ date('Y') }} <strong>DiscovTrip</strong>.
                Fait avec <i class="fas fa-heart" aria-hidden="true"></i> au Bénin.
                Tous droits réservés.
            </p>
            <div class="dt-footer-trust" aria-label="Garanties">
                <div class="dt-footer-trust-item">
                    <i class="fas fa-shield-alt dt-footer-trust-item--foret" aria-hidden="true"></i>
                    <span>Paiement sécurisé</span>
                </div>
                <div class="dt-footer-trust-item">
                    <i class="fas fa-headset dt-footer-trust-item--ambre" aria-hidden="true"></i>
                    <span>Support 24/7</span>
                </div>
                <div class="dt-footer-trust-item">
                    <i class="fas fa-undo-alt dt-footer-trust-item--foret" aria-hidden="true"></i>
                    <span>Annulation gratuite</span>
                </div>
            </div>
        </div>
    </div>
</footer>

{{-- ════════════════════════════════════════
     SCRIPTS GLOBAUX
════════════════════════════════════════ --}}
@stack('scripts')

<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
(function () {
    'use strict';

    /* ── Scroll Reveal ── */
    if ('IntersectionObserver' in window) {
        var revealObs = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) {
                    e.target.classList.add('dt-reveal--in');
                    revealObs.unobserve(e.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -48px 0px' });

        document.querySelectorAll('.dt-reveal, .dt-reveal-left, .dt-reveal-right').forEach(function (el) {
            revealObs.observe(el);
        });
    } else {
        document.querySelectorAll('.dt-reveal, .dt-reveal-left, .dt-reveal-right').forEach(function (el) {
            el.classList.add('dt-reveal--in');
        });
    }

    /* ── Hero image loaded ── */
    var heroImg = document.getElementById('dt-hero-img');
    if (heroImg) {
        if (heroImg.complete && heroImg.naturalWidth > 0) {
            heroImg.classList.add('is-loaded');
        } else {
            heroImg.addEventListener('load',  function () { this.classList.add('is-loaded'); });
            heroImg.addEventListener('error', function () { this.parentElement && this.parentElement.classList.add('img-error'); });
        }
    }

    /* ── CSRF global ── */
    var meta = document.querySelector('meta[name="csrf-token"]');
    window.__CSRF__ = meta ? meta.getAttribute('content') : '';

    /* ── Wishlist AJAX ── */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-wishlist-id]');
        if (!btn || btn.dataset.loading) return;
        e.preventDefault();

        btn.dataset.loading = '1';
        var icon = btn.querySelector('i');

        fetch('/wishlist/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.__CSRF__,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ offer_id: btn.dataset.wishlistId }),
            credentials: 'same-origin',
        })
        .then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function (data) {
            var added = !!data.added;
            btn.classList.toggle('is-active', added);
            btn.setAttribute('aria-pressed', added.toString());
            if (icon) { icon.className = added ? 'fas fa-heart' : 'far fa-heart'; }
        })
        .catch(function (err) { console.warn('[Wishlist]', err); })
        .finally(function () { delete btn.dataset.loading; });
    });

    /* ── Newsletter AJAX ── */
    document.querySelectorAll('[data-newsletter]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var submit = form.querySelector('[type="submit"]');
            var emailInput = form.querySelector('[name="email"]');
            if (!emailInput || !emailInput.value.trim()) return;

            var origHTML = submit ? submit.innerHTML : '';
            if (submit) {
                submit.disabled = true;
                submit.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:8px" aria-hidden="true"></i>Envoi…';
            }

            fetch(form.getAttribute('action') || '/newsletter/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.__CSRF__,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ email: emailInput.value.trim() }),
                credentials: 'same-origin',
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success || data.message) {
                    emailInput.value = '';
                    if (submit) {
                        submit.innerHTML = '<i class="fas fa-check" style="margin-right:8px" aria-hidden="true"></i>Inscrit !';
                    }
                } else {
                    if (submit) { submit.disabled = false; submit.innerHTML = origHTML; }
                }
            })
            .catch(function () {
                if (submit) { submit.disabled = false; submit.innerHTML = origHTML; }
            });
        });
    });

})();
</script>

{{-- ════════════════════════════════════════
     SCROLL TO TOP
════════════════════════════════════════ --}}
<button id="dt-scroll-top"
        aria-label="Retour en haut de page"
        onclick="window.scrollTo({top:0,behavior:'smooth'})"
        style="
            position:fixed;bottom:100px;right:28px;z-index:9000;
            width:44px;height:44px;border-radius:50%;
            background:var(--f-700,#1F6B44);color:#fff;
            border:none;cursor:pointer;
            box-shadow:0 4px 16px rgba(0,0,0,.25);
            display:flex;align-items:center;justify-content:center;
            opacity:0;transform:translateY(12px);
            transition:opacity .3s,transform .3s;
            pointer-events:none;
        ">
    <i class="fas fa-arrow-up" style="font-size:14px;" aria-hidden="true"></i>
</button>
<script>
(function(){
    var btn = document.getElementById('dt-scroll-top');
    if (!btn) return;
    window.addEventListener('scroll', function(){
        var show = window.scrollY > 400;
        btn.style.opacity      = show ? '1' : '0';
        btn.style.transform    = show ? 'translateY(0)' : 'translateY(12px)';
        btn.style.pointerEvents= show ? 'auto' : 'none';
    }, { passive: true });
})();
</script>

{{-- ════════════════════════════════════════
     CHATBOT — DiscovGuide
════════════════════════════════════════ --}}
@include('components._chatbot')

</body>
</html>