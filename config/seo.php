<?php

/**
 * ═══════════════════════════════════════════════════════════════════════════════
 * DISCOVTRIP — Configuration SEO
 * ═══════════════════════════════════════════════════════════════════════════════
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Meta Tags par défaut
    |--------------------------------------------------------------------------
    */

    'default_title' => env('SEO_DEFAULT_TITLE', 'DiscovTrip — Expériences authentiques au Bénin'),

    'title_separator' => ' — ',
    'title_suffix'    => 'DiscovTrip',

    'default_description' => env(
        'SEO_DEFAULT_DESCRIPTION',
        "Réservez des expériences authentiques au Bénin avec des guides locaux certifiés. Culture, gastronomie, nature, aventure."
    ),

    'default_keywords' => env(
        'SEO_DEFAULT_KEYWORDS',
        'bénin,expériences,tourisme,guides locaux,cotonou,ouidah,abomey,réservation,voyage afrique'
    ),

    // CORRECTION : utiliser config() au lieu de env() pour éviter null si config cachée
    'default_image' => config('app.url') . '/images/og-discovtrip.jpg',

    /*
    |--------------------------------------------------------------------------
    | Robots
    |--------------------------------------------------------------------------
    */

    'robots' => [
        'index'  => env('SEO_ROBOTS_INDEX',  true),
        'follow' => env('SEO_ROBOTS_FOLLOW', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Open Graph (Facebook, LinkedIn, WhatsApp)
    |--------------------------------------------------------------------------
    */

    'og' => [
        'site_name'    => 'DiscovTrip',   // CORRECTION : majuscule T
        'type'         => 'website',
        'locale'       => 'fr_FR',
        'image_width'  => 1200,
        'image_height' => 630,
    ],

    /*
    |--------------------------------------------------------------------------
    | Twitter Card
    |--------------------------------------------------------------------------
    */

    'twitter' => [
        'card'    => 'summary_large_image',
        'site'    => env('SEO_TWITTER_HANDLE', '@discovtrip'),
        'creator' => env('SEO_TWITTER_HANDLE', '@discovtrip'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Schema.org — Organisation (JSON-LD)
    |--------------------------------------------------------------------------
    */

    'schema' => [
        'enabled' => true,
        'organization' => [
            '@type'  => 'TravelAgency',   // Plus précis qu'Organization
            'name'   => 'DiscovTrip',
            'url'    => env('APP_URL'),
            'logo'   => env('APP_URL') . '/images/logo.svg',
            'sameAs' => array_filter([
                env('DISCOVTRIP_SOCIAL_FACEBOOK',  ''),
                env('DISCOVTRIP_SOCIAL_INSTAGRAM', ''),
                env('DISCOVTRIP_SOCIAL_TIKTOK',    ''),
            ]),
            'contactPoint' => [
                '@type'           => 'ContactPoint',
                'telephone'       => env('DISCOVTRIP_WHATSAPP_PHONE', '+229 01 00 00 00 00'),
                'contactType'     => 'customer service',
                'areaServed'      => 'BJ',
                'availableLanguage' => ['French', 'English'],
                'contactOption'   => 'TollFree',
            ],
            'address' => [
                '@type'           => 'PostalAddress',
                'addressLocality' => 'Cotonou',
                'addressCountry'  => 'BJ',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sitemap
    |--------------------------------------------------------------------------
    */

    'sitemap' => [
        'enabled' => env('SEO_SITEMAP_AUTO_GENERATE', true),
        'cache_ttl' => 86400, // 24h en secondes
        'change_frequency' => [
            'home'   => 'daily',
            'offers' => 'daily',
            'cities' => 'weekly',
            'static' => 'monthly',
        ],
        'priority' => [
            'home'   => 1.0,
            'offers' => 0.9,
            'cities' => 0.8,
            'static' => 0.5,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Canonical URLs
    |--------------------------------------------------------------------------
    */

    'canonical' => [
        'enabled'     => true,
        'force_https' => env('FORCE_HTTPS', false),
        'force_www'   => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Breadcrumbs (Schema.org BreadcrumbList)
    |--------------------------------------------------------------------------
    */

    'breadcrumbs' => [
        'enabled'        => true,
        'schema_enabled' => true,
        'home_title'     => 'Accueil',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination SEO
    |--------------------------------------------------------------------------
    */

    'pagination' => [
        'rel_prev_next'           => true,
        'canonical_on_first_page' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance
    |--------------------------------------------------------------------------
    */

    'performance' => [
        'minify_html' => env('APP_ENV') === 'production',
        'preconnect_domains' => [
            'https://fonts.googleapis.com',
            'https://fonts.gstatic.com',
            'https://cdnjs.cloudflare.com',
        ],
        'dns_prefetch_domains' => [
            'https://www.google-analytics.com',
            'https://www.googletagmanager.com',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking & Analytics
    |--------------------------------------------------------------------------
    */

    'tracking' => [
        'google_analytics_id'      => env('GOOGLE_ANALYTICS_ID'),
        'google_tag_manager_id'    => env('GOOGLE_TAG_MANAGER_ID'),
        'facebook_pixel_id'        => env('FACEBOOK_PIXEL_ID'),
        'enabled_in_production_only' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Chemins à exclure de l'indexation (noindex)
    |--------------------------------------------------------------------------
    */

    'noindex_paths' => [
        '/account/*',
        '/admin/*',
        '/filament/*',
        '/payment/*',
        '/api/*',
        '/bookings/*/cancel',
        '/bookings/*/pdf',
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirections 301 permanentes
    |--------------------------------------------------------------------------
    */

    'redirects' => [
        // '/ancienne-url' => '/nouvelle-url',
    ],

];