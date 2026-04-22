<?php

/**
 * DiscovTrip — Helpers globaux
 *
 * mediaUrl() : gère les URLs d'images quel que soit le driver de stockage.
 * Fonctionne avec disk 'public' (local) ET disk 'cloudinary'.
 *
 * Utilisation dans les vues Blade :
 *   {{ mediaUrl($offer->cover_image) }}
 *   {{ mediaUrl($offer->cover_image, asset('images/placeholder.jpg')) }}
 */

if (! function_exists('mediaUrl')) {
    function mediaUrl(?string $path, string $fallback = ''): string
    {
        if (! $path) {
            return $fallback;
        }

        // Déjà une URL complète (Cloudinary CDN, S3, etc.) — retourner tel quel
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        try {
            $disk = config('filesystems.default', 'public');
            return \Illuminate\Support\Facades\Storage::disk($disk)->url($path);
        } catch (\Throwable $e) {
            // Fallback sur le disk public si le disk par défaut échoue
            try {
                return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
            } catch (\Throwable $e2) {
                return asset('storage/' . $path);
            }
        }
    }
}
