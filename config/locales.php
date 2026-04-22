<?php

/**
 * ═══════════════════════════════════════════════════════════════════════════════
 * CONFIGURATION MULTI-LANGUES
 * ═══════════════════════════════════════════════════════════════════════════════
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Langues Supportées
    |--------------------------------------------------------------------------
    */
    'supported' => [
        'fr' => [
            'name' => 'Français',
            'native_name' => 'Français',
            'flag' => '🇫🇷',
            'locale' => 'fr_FR',
            'direction' => 'ltr',
            'enabled' => true,
        ],
        'en' => [
            'name' => 'English',
            'native_name' => 'English',
            'flag' => '🇬🇧',
            'locale' => 'en_GB',
            'direction' => 'ltr',
            'enabled' => true,
        ],
        'es' => [
            'name' => 'Español',
            'native_name' => 'Español',
            'flag' => '🇪🇸',
            'locale' => 'es_ES',
            'direction' => 'ltr',
            'enabled' => false,
        ],
        'pt' => [
            'name' => 'Português',
            'native_name' => 'Português',
            'flag' => '🇵🇹',
            'locale' => 'pt_PT',
            'direction' => 'ltr',
            'enabled' => false,
        ],
        'ar' => [
            'name' => 'Arabic',
            'native_name' => 'العربية',
            'flag' => '🇸🇦',
            'locale' => 'ar_SA',
            'direction' => 'rtl',
            'enabled' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Langue par Défaut
    |--------------------------------------------------------------------------
    */
    'default' => env('APP_LOCALE', 'fr'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Locale
    |--------------------------------------------------------------------------
    */
    'fallback' => env('APP_FALLBACK_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Détection Automatique
    |--------------------------------------------------------------------------
    */
    'auto_detect' => true,

    /*
    |--------------------------------------------------------------------------
    | Sources de Détection (ordre de priorité)
    |--------------------------------------------------------------------------
    */
    'detection_sources' => [
        'cookie',      // Cookie locale
        'session',     // Session
        'url',         // Paramètre URL (?lang=fr)
        'header',      // Accept-Language header
        'default',     // Langue par défaut
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookie
    |--------------------------------------------------------------------------
    */
    'cookie' => [
        'name' => 'locale',
        'lifetime' => 525600, // 1 an
    ],

    /*
    |--------------------------------------------------------------------------
    | URLs Localisées
    |--------------------------------------------------------------------------
    */
    'localized_urls' => false, // true pour /fr/offers, /en/offers

    /*
    |--------------------------------------------------------------------------
    | Traductions Manquantes
    |--------------------------------------------------------------------------
    */
    'missing_translations' => [
        'log' => true,
        'fallback_to_key' => true,
    ],

];
