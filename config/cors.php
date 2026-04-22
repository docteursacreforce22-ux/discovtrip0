<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration stricte CORS pour production.
    | Autorise uniquement les domaines de confiance.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => env('CORS_ALLOWED_ORIGINS') 
        ? explode(',', env('CORS_ALLOWED_ORIGINS'))
        : [
            // Production
            'https://discovtrip.com',
            'https://www.discovtrip.com',
            'https://app.discovtrip.com',
            'https://admin.discovtrip.com',
            
            // Staging
            'https://staging.discovtrip.com',
            
            // Development (à retirer en production)
            'http://localhost:3000',
            'http://localhost:8000',
            'http://localhost',
            'http://127.0.0.1:3000',
            'http://127.0.0.1:8000',
        ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Accept',
        'Accept-Language',
        'Content-Type',
        'Content-Language',
        'Authorization',
        'X-Requested-With',
        'X-CSRF-Token',
    ],

    'exposed_headers' => [
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'X-RateLimit-Reset',
    ],

    'max_age' => 86400, // 24 heures

    'supports_credentials' => true,

];
