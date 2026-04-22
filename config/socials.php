<?php

/**
 * Configuration des réseaux sociaux DiscovTrip
 * ─────────────────────────────────────────────
 * Définissez vos URLs dans le fichier .env :
 *
 *   SOCIAL_INSTAGRAM=https://www.instagram.com/discovtrip
 *   SOCIAL_FACEBOOK=https://www.facebook.com/discovtrip
 *   SOCIAL_TIKTOK=https://www.tiktok.com/@discovtrip
 *   SOCIAL_YOUTUBE=https://www.youtube.com/@discovtrip
 *   SOCIAL_WHATSAPP=https://wa.me/22900000000
 *
 * Laissez vide (ou retirez la ligne) pour masquer le bouton.
 */

return [

    'instagram' => [
        'url'   => env('SOCIAL_INSTAGRAM', ''),
        'icon'  => 'fa-instagram',
        'label' => 'Instagram',
        'color' => '#E1306C',        // couleur hover au survol
    ],

    'facebook' => [
        'url'   => env('SOCIAL_FACEBOOK', ''),
        'icon'  => 'fa-facebook-f',
        'label' => 'Facebook',
        'color' => '#1877F2',
    ],

    'tiktok' => [
        'url'   => env('SOCIAL_TIKTOK', ''),
        'icon'  => 'fa-tiktok',
        'label' => 'TikTok',
        'color' => '#010101',
    ],

    'youtube' => [
        'url'   => env('SOCIAL_YOUTUBE', ''),
        'icon'  => 'fa-youtube',
        'label' => 'YouTube',
        'color' => '#FF0000',
    ],

    'whatsapp' => [
        'url'   => env('SOCIAL_WHATSAPP', ''),
        'icon'  => 'fa-whatsapp',
        'label' => 'WhatsApp',
        'color' => '#25D366',
    ],

    'linkedin' => [
        'url'   => env('SOCIAL_LINKEDIN', ''),
        'icon'  => 'fa-linkedin-in',
        'label' => 'LinkedIn',
        'color' => '#0A66C2',
    ],

];