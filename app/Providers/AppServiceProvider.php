<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use App\Models\Offer;
use App\Models\SiteSetting;
use App\Observers\OfferObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }

        Blade::anonymousComponentPath(
            resource_path('views/emails'),
            'emails'
        );

        Offer::observe(OfferObserver::class);

        // Directive Blade @heroImage('key', 'fallback')
        Blade::directive('heroImage', function (string $expression) {
            return "<?php echo App\\Providers\\AppServiceProvider::heroImage({$expression}); ?>";
        });
    }

    /**
     * Retourne l'URL d'une image hero gérée en admin.
     *
     * - Cache 1 heure (invalidé à la sauvegarde dans HeroSettings)
     * - Compatible disk 'public' (local) ET disk 'cloudinary'
     * - Si la valeur stockée est déjà une URL complète → retournée telle quelle
     */
    public static function heroImage(string $key, string $fallback = ''): ?string
    {
        try {
            $storedValue = Cache::remember('hero.' . $key, 3600, function () use ($key) {
                return SiteSetting::where('key', $key)->value('value');
            });

            if (! $storedValue) {
                return $fallback ?: null;
            }

            // Déjà une URL complète (Cloudinary CDN, S3…) — retourner tel quel
            if (str_starts_with($storedValue, 'http://') || str_starts_with($storedValue, 'https://')) {
                return $storedValue;
            }

            // Chemin relatif → générer l'URL via le disk par défaut
            $disk = config('filesystems.default', 'public');
            return Storage::disk($disk)->url($storedValue);

        } catch (\Throwable $e) {
            // DB pas encore disponible ou disk non configuré
            return $fallback ?: null;
        }
    }
}
