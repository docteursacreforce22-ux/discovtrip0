<?php

/**
 * ═══════════════════════════════════════════════════════════════════════════════
 * config/security.php — DiscovTrip
 * Configuration sécurité corrigée : CSP élargie pour tous les CDN légitimes
 * ═══════════════════════════════════════════════════════════════════════════════
 */

return [

    // ═══════════════════════════════════════════════════════════════════════════
    // CONTENT SECURITY POLICY (CSP)
    // ═══════════════════════════════════════════════════════════════════════════
    'csp' => [
        'enabled' => env('SECURITY_CSP_ENABLED', true),

        'directives' => [

            // Sources par défaut
            "default-src" => ["'self'"],

            // Scripts autorisés
            "script-src" => [
                "'self'",
                "'unsafe-inline'",                   // Alpine.js + NProgress inline
                "https://cdnjs.cloudflare.com",      // Font Awesome JS (si besoin)
                "https://cdn.jsdelivr.net",           // NProgress
                "https://js.stripe.com",              // Stripe.js
                "https://widget.kkiapay.me",          // KKiaPay widget
                "https://cdn.kkiapay.me",             // KKiaPay SDK
            ],

            // Styles autorisés
            "style-src" => [
                "'self'",
                "'unsafe-inline'",                   // Styles inline Blade
                "https://fonts.googleapis.com",       // Google Fonts
                "https://cdnjs.cloudflare.com",      // ← Font Awesome CSS (AJOUTÉ)
                "https://cdn.jsdelivr.net",           // ← NProgress CSS (AJOUTÉ)
            ],

            // Images — permissif (Cloudinary, assets, favicons, data URIs)
            "img-src" => [
                "'self'",
                "data:",
                "blob:",
                "https:",
            ],

            // Fonts — Google Fonts + Font Awesome woff2 (AJOUTÉ)
            "font-src" => [
                "'self'",
                "data:",
                "https://fonts.gstatic.com",
                "https://cdnjs.cloudflare.com",      // ← Font Awesome .woff2 (AJOUTÉ)
            ],

            // Connexions XHR / fetch / WebSocket
            "connect-src" => [
                "'self'",
                "https://api.stripe.com",
                "https://api.groq.com",              // Chatbot DiscovGuide
                "https://sandbox.api.kkiapay.me",
                "https://api.kkiapay.me",
            ],

            // Iframes autorisées
            "frame-src" => [
                "https://js.stripe.com",
                "https://widget.kkiapay.me",
                "https://www.youtube.com",           // Vidéos offres
                "https://player.vimeo.com",
            ],

            // Anti-clickjacking
            "frame-ancestors" => ["'none'"],

            // Base URL
            "base-uri" => ["'self'"],

            // Soumissions de formulaires
            "form-action" => ["'self'"],

            // Force HTTPS pour toutes les ressources embarquées
            "upgrade-insecure-requests" => [],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════════
    // HSTS
    // ═══════════════════════════════════════════════════════════════════════════
    'hsts' => [
        'enabled'            => env('SECURITY_HSTS_ENABLED', true),
        'max_age'            => env('SECURITY_HSTS_MAX_AGE', 31536000),
        'include_subdomains' => env('SECURITY_HSTS_SUBDOMAINS', true),
        'preload'            => env('SECURITY_HSTS_PRELOAD', false),
    ],

    // ═══════════════════════════════════════════════════════════════════════════
    // RATE LIMITING
    // ═══════════════════════════════════════════════════════════════════════════
    'rate_limiting' => [
        'enabled'       => env('RATE_LIMITING_ENABLED', true),
        'max_attempts'  => env('RATE_LIMIT_MAX', 60),
        'decay_minutes' => env('RATE_LIMIT_DECAY', 1),
    ],

    // ═══════════════════════════════════════════════════════════════════════════
    // IP FILTERING
    // ═══════════════════════════════════════════════════════════════════════════
    'ip_filtering' => [
        'enabled'         => env('IP_FILTERING_ENABLED', false),
        'blacklisted_ips' => [],
    ],

    // ═══════════════════════════════════════════════════════════════════════════
    // API
    // ═══════════════════════════════════════════════════════════════════════════
    'api' => [
        'force_https'   => env('FORCE_HTTPS', true),
        'allowed_hosts' => explode(',', env('ALLOWED_HOSTS', '')),
    ],

    // ═══════════════════════════════════════════════════════════════════════════
    // CSRF EXEMPTIONS
    // ═══════════════════════════════════════════════════════════════════════════
    'csrf' => [
        'exempt' => [
            'api/*',
            'webhooks/*',
        ],
    ],
];
