<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    */
    'name' => env('APP_NAME', 'Discovtrip'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    */
    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    */
    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    */
    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    */
    'timezone' => env('APP_TIMEZONE', 'Africa/Porto-Novo'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    */
    'locale' => env('APP_LOCALE', 'fr'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => 'fr_FR',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    */
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    */
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    */
    'providers' => \Illuminate\Support\ServiceProvider::defaultProviders()->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    */
    'aliases' => \Illuminate\Support\Facades\Facade::defaultAliases()->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Discovtrip Custom Settings
    |--------------------------------------------------------------------------
    */
    'supported_locales' => explode(',', env('SUPPORTED_LOCALES', 'fr,en')),

    'force_https' => env('FORCE_HTTPS', false),

    'hash_algo' => env('HASH_ALGO', 'argon2id'),

    'upload' => [
        'max_size' => env('UPLOAD_MAX_SIZE', 5242880),
        'allowed_images' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        'allowed_documents' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
        'allowed_all' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'doc', 'docx'],
    ],

    'rate_limit' => [
        'login_attempts' => env('RATE_LIMIT_LOGIN_ATTEMPTS', 5),
        'login_decay_minutes' => env('RATE_LIMIT_LOGIN_DECAY', 15),
        'api_requests_per_minute' => env('RATE_LIMIT_API_REQUESTS', 60),
    ],

    'security_headers' => [
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'geolocation=(self), microphone=()',
    ],

    'features' => [
        'multi_language' => env('MULTI_LANGUAGE_ENABLED', false),
        'multi_currency' => env('MULTI_CURRENCY_ENABLED', false),
        'ia_recommendations' => env('IA_RECOMMENDATION_ENABLED', true),
        'ia_content_generation' => env('IA_CONTENT_GENERATION_ENABLED', false),
    ],

];