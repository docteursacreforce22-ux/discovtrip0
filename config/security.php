<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration centralisée de toutes les options de sécurité.
    |
    */

    // ═══════════════════════════════════════════════════════════════════════
    // CONTENT SECURITY POLICY (CSP)
    // ═══════════════════════════════════════════════════════════════════════
    
    'csp' => [
        'enabled' => env('SECURITY_CSP_ENABLED', true),
        
        'directives' => [
            "default-src" => ["'self'"],
            "script-src" => [
                "'self'",
                env('APP_ENV') === 'local' ? "'unsafe-inline'" : "",
                "https://cdnjs.cloudflare.com",
                "https://cdn.jsdelivr.net",
            ],
            "style-src" => [
                "'self'",
                "'unsafe-inline'", // Nécessaire pour certains frameworks CSS
                "https://fonts.googleapis.com",
            ],
            "img-src" => [
                "'self'",
                "data:",
                "https:",
                "blob:",
            ],
            "font-src" => [
                "'self'",
                "data:",
                "https://fonts.gstatic.com",
            ],
            "connect-src" => [
                "'self'",
                env('APP_URL'),
                env('FRONTEND_URL'),
            ],
            "frame-ancestors" => ["'none'"],
            "base-uri" => ["'self'"],
            "form-action" => ["'self'"],
            "upgrade-insecure-requests" => [],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // HTTP STRICT TRANSPORT SECURITY (HSTS)
    // ═══════════════════════════════════════════════════════════════════════
    
    'hsts' => [
        'enabled' => env('SECURITY_HSTS_ENABLED', true),
        'max_age' => env('SECURITY_HSTS_MAX_AGE', 31536000), // 1 an
        'include_subdomains' => env('SECURITY_HSTS_SUBDOMAINS', true),
        'preload' => env('SECURITY_HSTS_PRELOAD', false),
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // CSRF PROTECTION
    // ═══════════════════════════════════════════════════════════════════════
    
    'csrf' => [
        'enabled' => env('SECURITY_CSRF_ENABLED', true),
        
        // Chemins exemptés de vérification CSRF
        'exempt' => [
            'api/*', // API utilise JWT, pas de CSRF
            'webhooks/*',
        ],
        
        // Durée de vie token CSRF (minutes)
        'lifetime' => 120,
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // PASSWORD POLICY
    // ═══════════════════════════════════════════════════════════════════════
    
    'password' => [
        'min_length' => env('PASSWORD_MIN_LENGTH', 12),
        'require_uppercase' => env('PASSWORD_REQUIRE_UPPERCASE', true),
        'require_lowercase' => env('PASSWORD_REQUIRE_LOWERCASE', true),
        'require_numbers' => env('PASSWORD_REQUIRE_NUMBERS', true),
        'require_special' => env('PASSWORD_REQUIRE_SPECIAL', true),
        'max_age_days' => env('PASSWORD_MAX_AGE_DAYS', null), // null = pas d'expiration
        'prevent_reuse' => env('PASSWORD_PREVENT_REUSE', 3), // Derniers N passwords
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // ACCOUNT LOCKING
    // ═══════════════════════════════════════════════════════════════════════
    
    'account_locking' => [
        'enabled' => env('SECURITY_ACCOUNT_LOCKING_ENABLED', true),
        'max_attempts' => env('SECURITY_MAX_LOGIN_ATTEMPTS', 5),
        'lockout_duration' => env('SECURITY_LOCKOUT_DURATION', 30), // minutes
        'progressive_lockout' => true, // Durée augmente à chaque tentative
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // SESSION SECURITY
    // ═══════════════════════════════════════════════════════════════════════
    
    'session' => [
        'secure_cookies' => env('SESSION_SECURE_COOKIE', true),
        'http_only_cookies' => true,
        'same_site_cookies' => 'lax', // lax, strict, none
        'max_sessions_per_user' => env('MAX_SESSIONS_PER_USER', 5),
        'absolute_timeout' => env('SESSION_ABSOLUTE_TIMEOUT', 480), // 8 heures
        'idle_timeout' => env('SESSION_IDLE_TIMEOUT', 120), // 2 heures
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // FILE UPLOAD SECURITY
    // ═══════════════════════════════════════════════════════════════════════
    
    'upload' => [
        'max_file_size' => env('UPLOAD_MAX_FILE_SIZE', 20 * 1024 * 1024), // 20 MB
        'allowed_extensions' => [
            // Images
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
            // Documents
            'pdf', 'doc', 'docx', 'xls', 'xlsx',
            // Autres
            'txt', 'csv',
        ],
        'allowed_mimetypes' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'text/plain',
            'text/csv',
        ],
        'scan_for_viruses' => env('UPLOAD_VIRUS_SCAN', false),
        'check_image_dimensions' => true,
        'max_image_width' => 4096,
        'max_image_height' => 4096,
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // IP WHITELISTING / BLACKLISTING
    // ═══════════════════════════════════════════════════════════════════════
    
    'ip_filtering' => [
        'enabled' => env('SECURITY_IP_FILTERING_ENABLED', false),
        
        // IPs autorisées (whitelist) - si défini, seules ces IPs peuvent accéder
        'whitelist' => env('SECURITY_IP_WHITELIST') 
            ? explode(',', env('SECURITY_IP_WHITELIST'))
            : [],
        
        // IPs bloquées (blacklist)
        'blacklist' => env('SECURITY_IP_BLACKLIST')
            ? explode(',', env('SECURITY_IP_BLACKLIST'))
            : [],
        
        // Chemins exemptés de filtrage IP
        'exempt_paths' => [
            'api/health',
            'webhooks/*',
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // API SECURITY
    // ═══════════════════════════════════════════════════════════════════════
    
    'api' => [
        // Exiger HTTPS en production
        'force_https' => env('API_FORCE_HTTPS', env('APP_ENV') === 'production'),
        
        // JWT
        'jwt_secret' => env('JWT_SECRET'),
        'jwt_ttl' => env('JWT_TTL', 60), // minutes
        'jwt_refresh_ttl' => env('JWT_REFRESH_TTL', 20160), // 2 semaines
        
        // API Keys (optionnel)
        'require_api_key' => env('API_REQUIRE_KEY', false),
        'api_key_header' => 'X-API-Key',
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // LOGGING & MONITORING
    // ═══════════════════════════════════════════════════════════════════════
    
    'logging' => [
        // Logger toutes les tentatives de login échouées
        'log_failed_logins' => true,
        
        // Logger les tentatives XSS/SQL injection
        'log_security_violations' => true,
        
        // Logger les accès admin
        'log_admin_actions' => true,
        
        // Logger les changements de données sensibles
        'log_sensitive_changes' => true,
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // TWO-FACTOR AUTHENTICATION
    // ═══════════════════════════════════════════════════════════════════════
    
    '2fa' => [
        'enabled' => env('2FA_ENABLED', true),
        'required_for_roles' => ['admin', 'moderator'],
        'recovery_codes_count' => 8,
        'totp_window' => 1, // ±30 secondes
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // TRUSTED PROXIES
    // ═══════════════════════════════════════════════════════════════════════
    
    'proxies' => [
        'trust_all' => env('TRUST_PROXIES', false),
        'proxies' => env('TRUSTED_PROXIES') 
            ? explode(',', env('TRUSTED_PROXIES'))
            : [],
    ],

];
