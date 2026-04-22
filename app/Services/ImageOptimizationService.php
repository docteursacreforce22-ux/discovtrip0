<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Service d'optimisation d'images — sans dépendance externe.
 * Utilise les extensions PHP natives (GD) disponibles sur Hostinger.
 *
 * Usage dans un controller ou Filament :
 *   $path = ImageOptimizationService::store($file, 'offers/covers', 1200, 800);
 */
class ImageOptimizationService
{
    // Qualité JPEG (70–85 est le bon compromis taille/qualité)
    private const JPEG_QUALITY = 80;

    // Qualité PNG (0–9, 9 = compression max)
    private const PNG_COMPRESSION = 7;

    /**
     * Redimensionne, optimise et stocke une image.
     *
     * @param UploadedFile $file        Fichier uploadé
     * @param string       $directory   Dossier dans storage/app/public/
     * @param int          $maxWidth    Largeur max en pixels (0 = pas de limite)
     * @param int          $maxHeight   Hauteur max en pixels (0 = pas de limite)
     * @return string                   Chemin relatif (ex: offers/covers/abc123.jpg)
     */
    public static function store(
        UploadedFile $file,
        string $directory = 'uploads',
        int $maxWidth  = 1200,
        int $maxHeight = 900
    ): string {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename  = Str::uuid() . '.' . ($extension === 'jpeg' ? 'jpg' : $extension);
        $path      = $directory . '/' . $filename;

        // Si GD n'est pas disponible → stockage direct sans optimisation
        if (! extension_loaded('gd')) {
            $file->storeAs($directory, $filename, 'public');
            return $path;
        }

        $sourcePath = $file->getRealPath();
        [$origWidth, $origHeight, $type] = getimagesize($sourcePath);

        // Charger l'image source
        $source = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG  => imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($sourcePath) : null,
            IMAGETYPE_GIF  => imagecreatefromgif($sourcePath),
            default        => null,
        };

        // Si format non supporté → stockage direct
        if ($source === null) {
            $file->storeAs($directory, $filename, 'public');
            return $path;
        }

        // Calcul des nouvelles dimensions en conservant le ratio
        [$newWidth, $newHeight] = self::calculateDimensions(
            $origWidth, $origHeight, $maxWidth, $maxHeight
        );

        // Redimensionnement
        $resized = imagecreatetruecolor($newWidth, $newHeight);

        // Préserver la transparence pour PNG et WebP
        if (in_array($type, [IMAGETYPE_PNG, IMAGETYPE_WEBP])) {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // Capturer la sortie en buffer
        ob_start();
        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($resized, null, self::JPEG_QUALITY),
            IMAGETYPE_PNG  => imagepng($resized, null, self::PNG_COMPRESSION),
            IMAGETYPE_WEBP => function_exists('imagewebp') ? imagewebp($resized, null, 85) : imagejpeg($resized, null, self::JPEG_QUALITY),
            IMAGETYPE_GIF  => imagegif($resized),
            default        => imagejpeg($resized, null, self::JPEG_QUALITY),
        };
        $imageData = ob_get_clean();

        // Libérer la mémoire
        imagedestroy($source);
        imagedestroy($resized);

        // Stocker dans public disk
        Storage::disk('public')->put($path, $imageData);

        return $path;
    }

    /**
     * Stocke une image de profil utilisateur (carré 300×300).
     */
    public static function storeAvatar(UploadedFile $file): string
    {
        return self::store($file, 'profile-pictures', 300, 300);
    }

    /**
     * Stocke une image de couverture d'offre (1200×800).
     */
    public static function storeCover(UploadedFile $file, string $folder = 'offers/covers'): string
    {
        return self::store($file, $folder, 1200, 800);
    }

    /**
     * Calcule les nouvelles dimensions en respectant le ratio.
     */
    private static function calculateDimensions(
        int $origW, int $origH,
        int $maxW,  int $maxH
    ): array {
        // Pas de redimensionnement si l'image est déjà assez petite
        if (($maxW === 0 || $origW <= $maxW) && ($maxH === 0 || $origH <= $maxH)) {
            return [$origW, $origH];
        }

        $ratio = $origW / $origH;

        if ($maxW > 0 && $maxH > 0) {
            // Contraindre dans les deux dimensions
            if ($origW / $maxW > $origH / $maxH) {
                return [$maxW, (int) round($maxW / $ratio)];
            }
            return [(int) round($maxH * $ratio), $maxH];
        }

        if ($maxW > 0) {
            return [$maxW, (int) round($maxW / $ratio)];
        }

        return [(int) round($maxH * $ratio), $maxH];
    }
}