<?php

/**
 * ═══════════════════════════════════════════════════════════════════════════════
 * CONFIGURATION PAIEMENTS
 * ═══════════════════════════════════════════════════════════════════════════════
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Gateway par Défaut
    |--------------------------------------------------------------------------
    | Gateways disponibles : 'stripe', 'fedapay', 'paypal'
    */
    'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'stripe'),

    /*
    |--------------------------------------------------------------------------
    | Gateways Actifs
    |--------------------------------------------------------------------------
    */
    'enabled_gateways' => [
        'stripe' => true,
        'fedapay' => true,
        'paypal' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe
    |--------------------------------------------------------------------------
    */
    'stripe' => [
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY', 'XOF'),
        'api_version' => '2023-10-16',
    ],

    /*
    |--------------------------------------------------------------------------
    | FedaPay (Mobile Money Afrique)
    |--------------------------------------------------------------------------
    */
    'fedapay' => [
        'public_key' => env('FEDAPAY_PUBLIC_KEY'),
        'secret_key' => env('FEDAPAY_SECRET_KEY'),
        'environment' => env('FEDAPAY_ENVIRONMENT', 'sandbox'), // sandbox | live
        'currency' => env('FEDAPAY_CURRENCY', 'XOF'),
        'webhook_secret' => env('FEDAPAY_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PayPal
    |--------------------------------------------------------------------------
    */
    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_SECRET'),
        'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox | live
        'currency' => 'EUR',
    ],

    /*
    |--------------------------------------------------------------------------
    | Devises Supportées
    |--------------------------------------------------------------------------
    */
    'currencies' => [
        'XOF' => [
            'name' => 'Franc CFA (BCEAO)',
            'symbol' => 'CFA',
            'decimals' => 0,
            'gateways' => ['stripe', 'fedapay'],
        ],
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'decimals' => 2,
            'gateways' => ['stripe', 'paypal'],
        ],
        'USD' => [
            'name' => 'Dollar US',
            'symbol' => '$',
            'decimals' => 2,
            'gateways' => ['stripe', 'paypal'],
        ],
        'GBP' => [
            'name' => 'Livre Sterling',
            'symbol' => '£',
            'decimals' => 2,
            'gateways' => ['stripe'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Devise par Défaut
    |--------------------------------------------------------------------------
    */
    'default_currency' => env('DEFAULT_CURRENCY', 'XOF'),

    /*
    |--------------------------------------------------------------------------
    | Frais de Transaction
    |--------------------------------------------------------------------------
    */
    'fees' => [
        'stripe' => [
            'percentage' => 2.9, // %
            'fixed' => 0, // Montant fixe
        ],
        'fedapay' => [
            'percentage' => 2.5,
            'fixed' => 0,
        ],
        'paypal' => [
            'percentage' => 3.4,
            'fixed' => 0.35,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks
    |--------------------------------------------------------------------------
    */
    'webhooks' => [
        'stripe_url' => '/payment/webhook/stripe',
        'fedapay_url' => '/payment/webhook/fedapay',
        'paypal_url' => '/payment/webhook/paypal',
    ],

    /*
    |--------------------------------------------------------------------------
    | Remboursements
    |--------------------------------------------------------------------------
    */
    'refunds' => [
        'enabled' => true,
        'max_days' => 14, // Jours après réservation
        'auto_approve_under' => 10000, // XOF - Montant auto-approuvé
        'admin_approval_required' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Factures
    |--------------------------------------------------------------------------
    */
    'invoices' => [
        'enabled' => true,
        'auto_generate' => true,
        'prefix' => 'DT',
        'format' => 'PDF', // PDF | HTML
        'storage_path' => storage_path('pdf/invoices'),
        'logo_path' => public_path('assets/images/logo.svg'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paiements Récurrents (Future)
    |--------------------------------------------------------------------------
    */
    'subscriptions' => [
        'enabled' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Limites
    |--------------------------------------------------------------------------
    */
    'limits' => [
        'min_amount' => 1000, // XOF
        'max_amount' => 10000000, // XOF
        'max_attempts' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    */
    'timeout' => [
        'payment_session' => 1800, // 30 minutes
        'pending_confirmation' => 3600, // 1 heure
    ],

];
