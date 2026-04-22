<?php

namespace App\Observers;

use App\Models\Offer;
use Illuminate\Support\Facades\Storage;

class OfferObserver
{
    /**
     * Disk utilisé pour les images — même valeur que dans OfferResource.
     * On lit la config pour rester cohérent quelle que soit l'env.
     */
    private function disk(): string
    {
        return config('filesystems.default', 'public');
    }

    public function updated(Offer $offer): void
    {
        // ── Cover image
        if ($offer->wasChanged('cover_image')) {
            $old = $offer->getOriginal('cover_image');
            if ($old && $old !== $offer->cover_image) {
                $this->deleteFile($old);
            }
        }

        // ── Gallery : supprimer les images retirées
        if ($offer->wasChanged('gallery')) {
            $oldGallery = (array) ($offer->getOriginal('gallery') ?? []);
            $newGallery = (array) ($offer->gallery ?? []);

            if (count($oldGallery) === 1 && is_string($oldGallery[0])) {
                $decoded = json_decode($oldGallery[0], true);
                if (is_array($decoded)) $oldGallery = $decoded;
            }

            $removed = array_diff($oldGallery, $newGallery);
            foreach ($removed as $file) {
                if ($file) $this->deleteFile($file);
            }
        }
    }

    public function deleting(Offer $offer): void
    {
        if ($offer->cover_image) {
            $this->deleteFile($offer->cover_image);
        }

        foreach ((array) ($offer->gallery ?? []) as $image) {
            if ($image) $this->deleteFile($image);
        }
    }

    /**
     * Supprime un fichier en gérant les URLs complètes (Cloudinary) et les chemins relatifs.
     */
    private function deleteFile(string $path): void
    {
        try {
            // Les URLs complètes (Cloudinary) ne peuvent pas être supprimées
            // via Storage::delete() avec un path relatif — on ignore silencieusement
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return;
            }
            Storage::disk($this->disk())->delete($path);
        } catch (\Throwable $e) {
            // Ne pas faire crasher l'app si la suppression échoue
        }
    }
}
