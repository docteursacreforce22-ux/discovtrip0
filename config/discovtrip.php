<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identité
    |--------------------------------------------------------------------------
    */

    'name' => env('DISCOVTRIP_NAME', 'DiscovTrip'),

    /*
    |--------------------------------------------------------------------------
    | Contact
    |--------------------------------------------------------------------------
    */

    // Email admin — reçoit les notifications de réservation, annulation, etc.
    'contact_email' => env('DISCOVTRIP_CONTACT_EMAIL', 'contact@discovtrip.com'),

    // Numéro WhatsApp affiché (format lisible)  : +229 01 XX XX XX XX
    'whatsapp_phone' => env('DISCOVTRIP_WHATSAPP_PHONE', '+229 01 00 00 00 00'),

    // Même numéro sans espaces ni + — utilisé dans les liens wa.me/XXXXXXXXXX
    'whatsapp_phone_raw' => env('DISCOVTRIP_WHATSAPP_PHONE_RAW', '22901000000'),

    /*
    |--------------------------------------------------------------------------
    | Réseaux sociaux
    |--------------------------------------------------------------------------
    */

    'social' => [
        'facebook'  => env('DISCOVTRIP_SOCIAL_FACEBOOK',  ''),
        'instagram' => env('DISCOVTRIP_SOCIAL_INSTAGRAM', ''),
        'tiktok'    => env('DISCOVTRIP_SOCIAL_TIKTOK',    ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paiement & Devises
    |--------------------------------------------------------------------------
    */

    // Devise par défaut
    'currency' => env('DISCOVTRIP_CURRENCY', 'XOF'),

    // Taux fixe FCFA → EUR (parité fixe Zone franc CFA)
    'eur_rate' => 655.957,

    // Délai avant expiration d'un paiement en ligne non finalisé (en heures)
    'payment_expiry_hours' => (int) env('DISCOVTRIP_PAYMENT_EXPIRY_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Règles métier — Réservations
    |--------------------------------------------------------------------------
    */

    // Délai minimum entre la réservation et la date de l'expérience (en heures)
    'booking_min_hours' => (int) env('DISCOVTRIP_BOOKING_MIN_HOURS', 24),

    // Fenêtre d'annulation gratuite (en heures avant l'expérience)
    // Correspond à la règle dans BookingController@cancel
    'cancellation_free_hours' => (int) env('DISCOVTRIP_CANCELLATION_FREE_HOURS', 48),

    /*
    |--------------------------------------------------------------------------
    | Rappels automatiques (commande bookings:send-reminders)
    |--------------------------------------------------------------------------
    */

    // Nombre de jours avant l'expérience pour l'envoi du rappel client
    'reminder_days_before' => (int) env('DISCOVTRIP_REMINDER_DAYS', 1),

];