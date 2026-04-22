<?php

/**
 * ═══════════════════════════════════════════════════════════════════════════════
 * CONFIGURATION INTELLIGENCE ARTIFICIELLE
 * ═══════════════════════════════════════════════════════════════════════════════
 */

return [

    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORGANIZATION'),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 2000),
        'temperature' => 0.7,
        'timeout' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Recommandations
    |--------------------------------------------------------------------------
    */
    'recommendations' => [
        'enabled' => env('IA_RECOMMENDATION_ENABLED', true),
        
        // Algorithme : collaborative_filtering | content_based | hybrid
        'algorithm' => 'hybrid',
        
        // Seuil de similarité minimum (0-1)
        'min_similarity' => 0.7,
        
        // Nombre de recommandations
        'max_results' => 10,
        
        // Poids des facteurs
        'weights' => [
            'user_history' => 0.4,
            'content_similarity' => 0.3,
            'popularity' => 0.2,
            'location' => 0.1,
        ],
        
        // Cache des recommandations
        'cache_ttl' => 3600, // 1 heure
    ],

    /*
    |--------------------------------------------------------------------------
    | Génération de Contenu
    |--------------------------------------------------------------------------
    */
    'content_generation' => [
        'enabled' => env('IA_CONTENT_GENERATION_ENABLED', false),
        
        // Types de contenu générable
        'types' => [
            'offer_description' => true,
            'meta_description' => true,
            'blog_post' => false,
            'email_template' => false,
        ],
        
        // Paramètres génération
        'params' => [
            'tone' => 'professional', // professional | casual | enthusiastic
            'length' => 'medium', // short | medium | long
            'language' => 'fr',
        ],
        
        // Modération contenu généré
        'moderation' => [
            'enabled' => true,
            'auto_publish' => false, // Nécessite validation admin
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Analyse de Sentiment (Avis Clients)
    |--------------------------------------------------------------------------
    */
    'sentiment_analysis' => [
        'enabled' => true,
        
        // Auto-modération basée sur sentiment
        'auto_moderate' => [
            'enabled' => true,
            'flag_negative_threshold' => 0.3, // 0-1
        ],
        
        // Catégories de sentiment
        'categories' => [
            'positive' => 0.6,  // > 0.6 = positif
            'neutral' => 0.4,   // 0.4 - 0.6 = neutre
            'negative' => 0.4,  // < 0.4 = négatif
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pricing Dynamique
    |--------------------------------------------------------------------------
    */
    'dynamic_pricing' => [
        'enabled' => true,
        
        // Facteurs de prix
        'factors' => [
            'demand' => 0.4,      // Demande actuelle
            'seasonality' => 0.3,  // Saisonnalité
            'occupancy' => 0.2,    // Taux remplissage
            'competition' => 0.1,  // Prix concurrence
        ],
        
        // Limites ajustement (%)
        'limits' => [
            'min_decrease' => -30, // Max -30%
            'max_increase' => 50,  // Max +50%
        ],
        
        // Fréquence recalcul
        'recalculate_interval' => 3600, // 1 heure
    ],

    /*
    |--------------------------------------------------------------------------
    | Chatbot (Future)
    |--------------------------------------------------------------------------
    */
    'chatbot' => [
        'enabled' => false,
        'provider' => 'openai',
        'model' => 'gpt-3.5-turbo',
        'max_conversation_length' => 10,
        'fallback_to_human' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-Traduction (Future)
    |--------------------------------------------------------------------------
    */
    'translation' => [
        'enabled' => false,
        'provider' => 'google', // google | deepl | openai
        'auto_detect' => true,
        'target_languages' => ['en', 'es', 'pt'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Recognition
    |--------------------------------------------------------------------------
    */
    'image_recognition' => [
        'enabled' => false,
        'auto_tag' => false,
        'quality_check' => true,
        'inappropriate_content_detection' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Fraud Detection
    |--------------------------------------------------------------------------
    */
    'fraud_detection' => [
        'enabled' => true,
        
        // Signaux de fraude
        'signals' => [
            'multiple_failed_payments' => true,
            'suspicious_email_patterns' => true,
            'vpn_detection' => true,
            'rapid_booking_attempts' => true,
        ],
        
        // Actions automatiques
        'actions' => [
            'flag_suspicious' => true,
            'require_verification' => true,
            'auto_block' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | A/B Testing (Future)
    |--------------------------------------------------------------------------
    */
    'ab_testing' => [
        'enabled' => false,
        'experiments' => [
            // 'checkout_flow_v2' => ['control' => 0.5, 'variant' => 0.5],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logs & Monitoring
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => true,
        'log_requests' => true,
        'log_responses' => false, // Peut contenir des données sensibles
        'log_errors' => true,
    ],

];
