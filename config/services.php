<?php

/**
 * ═══════════════════════════════════════════════════════════════════════════════
 * CONFIGURATION SERVICES EXTERNES
 * ═══════════════════════════════════════════════════════════════════════════════
 */

return [

    /*
    |--------------------------------------------------------------------------
    | SMS Services
    |--------------------------------------------------------------------------
    */
    'sms' => [
        'driver' => env('SMS_DRIVER', 'twilio'),
        'from' => env('SMS_FROM'),

        'twilio' => [
            'sid' => env('TWILIO_SID'),
            'token' => env('TWILIO_TOKEN'),
            'phone' => env('TWILIO_PHONE'),
        ],

        'africastalking' => [
            'username' => env('AFRICASTALKING_USERNAME'),
            'api_key' => env('AFRICASTALKING_API_KEY'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cloud Storage
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'driver' => env('STORAGE_DRIVER', 'local'),

        's3' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'eu-west-1'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

        'cloudinary' => [
            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
            'api_key' => env('CLOUDINARY_API_KEY'),
            'api_secret' => env('CLOUDINARY_API_SECRET'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Services
    |--------------------------------------------------------------------------
    */
    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
        'analytics_id' => env('GOOGLE_ANALYTICS_ID'),
        'tag_manager_id' => env('GOOGLE_TAG_MANAGER_ID'),
        
        'recaptcha' => [
            'site_key' => env('GOOGLE_RECAPTCHA_SITE_KEY'),
            'secret_key' => env('GOOGLE_RECAPTCHA_SECRET_KEY'),
            'version' => 'v3',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IA & Machine Learning
    |--------------------------------------------------------------------------
    */
    'ia' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => 'gpt-4',
            'max_tokens' => 2000,
        ],

        'recommendations' => [
            'enabled' => env('IA_RECOMMENDATION_ENABLED', true),
            'algorithm' => 'collaborative_filtering', // collaborative_filtering | content_based
            'min_similarity' => 0.7,
        ],

        'content_generation' => [
            'enabled' => env('IA_CONTENT_GENERATION_ENABLED', false),
            'auto_translate' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring & Logs
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'sentry' => [
            'dsn' => env('SENTRY_DSN'),
            'environment' => env('APP_ENV'),
            'traces_sample_rate' => 1.0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Media APIs
    |--------------------------------------------------------------------------
    */
    'social' => [
        'facebook' => [
            'app_id' => env('FACEBOOK_APP_ID'),
            'app_secret' => env('FACEBOOK_APP_SECRET'),
            'pixel_id' => env('FACEBOOK_PIXEL_ID'),
        ],

        'twitter' => [
            'api_key' => env('TWITTER_API_KEY'),
        ],

        'instagram' => [
            'access_token' => env('INSTAGRAM_ACCESS_TOKEN'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks Sortants
    |--------------------------------------------------------------------------
    */
    'webhooks' => [
        'secret' => env('WEBHOOK_SECRET'),
        'endpoints' => [
            'payment' => env('WEBHOOK_PAYMENT_URL'),
            'booking' => env('WEBHOOK_BOOKING_URL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe
    |--------------------------------------------------------------------------
    */
    // Stripe — désactivé pour l'instant (pas éligible Bénin)
    // 'stripe' => [...],

    // FedaPay — désactivé pour l'instant
    // 'fedapay' => [...],

    'kkiapay' => [
        'public_key'  => env('KKIAPAY_PUBLIC_KEY', ''),
        'private_key' => env('KKIAPAY_PRIVATE_KEY', ''),
        // true = sandbox (tests), false = production (live)
        'sandbox'     => env('KKIAPAY_SANDBOX', true),
    ],


        /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

        // ──────────────────────────────────────────────
    // GROQ AI  (gratuit — console.groq.com)
    // ──────────────────────────────────────────────
    'groq' => [
        'key'   => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        //
        // Autres modèles disponibles sur Groq (gratuits) :
        //   'llama-3.1-8b-instant'     → ultra-rapide, qualité correcte
        //   'llama-3.3-70b-versatile'  → meilleure qualité (recommandé)
        //   'mixtral-8x7b-32768'       → bon pour le contenu long
        //   'gemma2-9b-it'             → léger et rapide
    ],

];