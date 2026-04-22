<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Offer;

class DestinationsController extends Controller
{
    public function index()
    {
        // ════════════════════════════════════════════════════
        // LOGIQUE MULTI-PAYS
        //
        // Si plusieurs pays actifs → affichage "pays" (futur)
        // Si 1 seul pays (situation actuelle Bénin) → affichage
        // direct des villes, exactement comme aujourd'hui.
        //
        // Quand tu signes des partenariats, tu crées le pays
        // via l'admin Filament → la page bascule automatiquement
        // en mode multi-pays SANS aucune modification de code.
        // ════════════════════════════════════════════════════

        $activeCountriesCount = Country::active()
            ->has('activeCities')
            ->count();

        $multiCountryMode = $activeCountriesCount > 1;

        if ($multiCountryMode) {
            return $this->indexMultiCountry();
        }

        return $this->indexSingleCountry();
    }

    // ────────────────────────────────────────────────────────
    // MODE ACTUEL : 1 seul pays → afficher les villes
    // (identique à ce qui existe, aucun changement visuel)
    // ────────────────────────────────────────────────────────

    private function indexSingleCountry()
    {
        $featuredCities = City::where('is_featured', true)
            ->where('is_active', true)
            ->withCount(['offers' => fn ($q) => $q->where('status', 'published')])
            ->withMin(
                ['offers as offers_min_base_price' => fn ($q) => $q->where('status', 'published')],
                'base_price'
            )
            ->orderBy('featured_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $cities = City::where('is_active', true)
            ->withCount(['offers' => fn ($q) => $q->where('status', 'published')])
            ->withMin(
                ['offers as offers_min_base_price' => fn ($q) => $q->where('status', 'published')],
                'base_price'
            )
            ->orderByDesc('is_featured')
            ->orderBy('featured_order', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(12);

        $heroCards     = $cities->getCollection()->take(2);
        $gridCards     = $cities->getCollection()->skip(2)->values();
        $totalOffers   = Offer::where('status', 'published')->count();
        $totalCities   = City::where('is_active', true)->count();
        $globalRating  = City::where('is_active', true)->where('average_rating', '>', 0)->avg('average_rating') ?? 4.8;
        $totalReviews  = class_exists(\App\Models\Review::class) ? \App\Models\Review::count() : 0;
        $activeViewers = 12 + (now()->hour % 12) + (now()->minute % 8);
        $heroImage     = 'images/hero.jpg';
        $multiCountryMode = false;

        return view('pages.destinations', compact(
            'featuredCities', 'cities', 'heroCards', 'gridCards',
            'totalOffers', 'totalCities', 'globalRating',
            'totalReviews', 'activeViewers', 'heroImage',
            'multiCountryMode',
        ));
    }

    // ────────────────────────────────────────────────────────
    // MODE FUTUR : plusieurs pays → grille de pays
    // ────────────────────────────────────────────────────────

    private function indexMultiCountry()
    {
        $countries = Country::active()
            ->withCount([
                'cities as cities_count' => fn ($q) => $q->where('is_active', true),
                'cities as offers_count' => fn ($q) => $q->where('is_active', true)
                    ->whereHas('offers', fn ($oq) => $oq->where('status', 'published')),
            ])
            ->orderByDesc('is_featured')
            ->orderBy('featured_order')
            ->orderBy('name')
            ->get();

        $totalOffers    = Offer::where('status', 'published')->count();
        $totalCities    = City::where('is_active', true)->count();
        $totalCountries = $countries->count();
        $multiCountryMode = true;

        return view('pages.destinations-countries', compact(
            'countries', 'totalOffers', 'totalCities', 'totalCountries',
            'multiCountryMode',
        ));
    }
}