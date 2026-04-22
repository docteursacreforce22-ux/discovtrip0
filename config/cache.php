<?php

/**
 * ═══════════════════════════════════════════════════════════════════════════════
 * CONFIGURATION CACHE
 * ═══════════════════════════════════════════════════════════════════════════════
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Driver de Cache par Défaut
    |--------------------------------------------------------------------------
    | Drivers disponibles : 'file', 'redis', 'memcached', 'array'
    */
    'default' => env('CACHE_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Préfixe Cache
    |--------------------------------------------------------------------------
    */
    'prefix' => env('CACHE_PREFIX', 'discovtrip_'),

    /*
    |--------------------------------------------------------------------------
    | TTL par Défaut (secondes)
    |--------------------------------------------------------------------------
    */
    'ttl' => env('CACHE_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Stores de Cache
    |--------------------------------------------------------------------------
    */
    'stores' => [

        'file' => [
            'driver' => 'file',
            'path' => storage_path('cache'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
        ],

        'memcached' => [
            'driver' => 'memcached',
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration Redis
    |--------------------------------------------------------------------------
    */
    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        
        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DATABASE', 0),
        ],

        'cache' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DATABASE', 1),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stratégies de Cache
    |--------------------------------------------------------------------------
    */
    'strategies' => [
        
        // Pages publiques
        'pages' => [
            'ttl' => 3600, // 1 heure
            'tags' => ['pages'],
        ],

        // Offres
        'offers' => [
            'ttl' => 1800, // 30 minutes
            'tags' => ['offers'],
        ],

        // Destinations (villes/pays/continents)
        'destinations' => [
            'ttl' => 7200, // 2 heures
            'tags' => ['destinations'],
        ],

        // Recherche
        'search' => [
            'ttl' => 600, // 10 minutes
            'tags' => ['search'],
        ],

        // Avis clients
        'reviews' => [
            'ttl' => 1800, // 30 minutes
            'tags' => ['reviews'],
        ],

        // Statistiques admin
        'stats' => [
            'ttl' => 300, // 5 minutes
            'tags' => ['stats'],
        ],

        // Configuration
        'config' => [
            'ttl' => 86400, // 24 heures
            'tags' => ['config'],
        ],

        // Sessions utilisateur
        'sessions' => [
            'ttl' => 7200, // 2 heures
            'tags' => ['sessions'],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Routes
    |--------------------------------------------------------------------------
    */
    'routes' => [
        'enabled' => env('APP_ENV') === 'production',
        'path' => storage_path('cache/routes.cache'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Vues
    |--------------------------------------------------------------------------
    */
    'views' => [
        'enabled' => env('APP_ENV') === 'production',
        'path' => storage_path('cache/views.cache'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Config
    |--------------------------------------------------------------------------
    */
    'config' => [
        'enabled' => env('APP_ENV') === 'production',
        'path' => storage_path('cache/config.cache'),
    ],

];
