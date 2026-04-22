<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Offer;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class OfferController extends Controller
{
    // ════════════════════════════════════════════════════════
    // INDEX — Catalogue
    // ════════════════════════════════════════════════════════

    public function index(Request $request): View
    {
        // ── Offres en promotion (requête séparée, affichées en bandeau)
        $promoOffers = Cache::remember(
            'offers.promo.' . md5($request->fullUrl()),
            120, // 2 minutes — promos changent fréquemment
            fn () => Offer::query()
                ->published()
                ->onPromo()
                ->with(['city'])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->orderByDesc('is_featured')
                ->limit(5)
                ->get()
        );

        $promoIds = $promoOffers->pluck('id');

        // ── Catalogue principal
        $query = Offer::query()
            ->published()
            ->with(['city'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        // Exclure les promos du catalogue principal (sauf si filtre promo actif)
        if (! $request->boolean('promo')) {
            $query->whereNotIn('id', $promoIds);
        }

        // ── Recherche textuelle — wildcards LIKE échappés (protection DoS)
        if ($search = $request->input('search')) {
            $s = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($search));
            $query->where(function ($q) use ($s) {
                $q->where('title',             'like', "%{$s}%")
                  ->orWhere('description',     'like', "%{$s}%")
                  ->orWhere('short_description','like', "%{$s}%")
                  ->orWhereHas('city', fn ($c) => $c->where('name', 'like', "%{$s}%"));
            });
        }

        // ── Filtres
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($cityId = $request->input('city')) {
            $query->where('city_id', (int) $cityId);
        }

        if ($minPrice = $request->input('min_price')) {
            $query->where('base_price', '>=', (float) $minPrice);
        }

        if ($maxPrice = $request->input('max_price')) {
            $query->where('base_price', '<=', (float) $maxPrice);
        }

        if ($request->boolean('instant')) {
            $query->where('is_instant_booking', true);
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->boolean('promo')) {
            $query->onPromo();
        }

        // ── Tri
        match ($request->input('sort', 'newest')) {
            'price_asc'  => $query->orderBy('base_price', 'asc'),
            'price_desc' => $query->orderBy('base_price', 'desc'),
            'rating'     => $query->orderByDesc('reviews_avg_rating')->orderByDesc('reviews_count'),
            default      => $query->orderByDesc('is_featured')->orderByDesc('published_at'),
        };

        $offers = $query->paginate(9)->withQueryString();

        // ── Sidebar villes — mise en cache 10 min
        $cities = Cache::remember('offers.index.cities', 600, fn () =>
            City::query()
                ->whereHas('offers', fn ($q) => $q->published())
                ->withCount(['offers' => fn ($q) => $q->published()])
                ->orderByDesc('offers_count')
                ->get()
        );

        // ── Stats globales
        $totalOffers = Cache::remember('offers.index.total', 300, fn () =>
            Offer::published()->count()
        );

        $avgRating = Cache::remember('offers.index.avg_rating', 600, fn () =>
            round(Review::avg('rating') ?? 4.8, 1)
        );

        // ── Images hero — 2 dernières offres publiées avec image (pas inRandomOrder)
        $heroOffers = Cache::remember('offers.index.hero', 600, fn () =>
            Offer::published()
                ->whereNotNull('cover_image')
                ->orderByDesc('published_at')
                ->limit(2)
                ->pluck('cover_image')
        );

        return view('pages.offers.index', [
            'offers'             => $offers,
            'promoOffers'        => $promoOffers,
            'cities'             => $cities,
            'totalOffers'        => $totalOffers,
            'avgRating'          => $avgRating,
            'heroImageMain'      => $heroOffers->get(0),
            'heroImageSecondary' => $heroOffers->get(1),
        ]);
    }

    // ════════════════════════════════════════════════════════
    // SHOW — Fiche offre
    // ════════════════════════════════════════════════════════

    public function show(string $slug): View
    {
        $offer = Offer::query()
            ->with([
                'city.country',
                'user',
                'activeTiers',
                // Charger UNIQUEMENT les 4 premiers avis publiés — évite de charger
                // toute la table en mémoire pour avg() côté PHP
                'reviews' => fn ($q) => $q->where('status', 'published')
                                          ->with('user:id,first_name,last_name')
                                          ->latest()
                                          ->limit(4),
            ])
            ->withCount(['reviews' => fn ($q) => $q->where('status', 'published')])
            ->withAvg(['reviews' => fn ($q) => $q->where('status', 'published')], 'rating')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // ── Incrémenter views_count via cache (évite un UPDATE SQL à chaque visite)
        // Le compteur est vidé vers la DB toutes les 5 minutes via schedule
        $cacheKey = 'offer.views.' . $offer->id;
        $pendingViews = Cache::increment($cacheKey);

        // Vider le cache vers la DB si seuil atteint (tous les 10 hits ou 5 min)
        if ($pendingViews >= 10) {
            $views = Cache::pull($cacheKey, 0);
            if ($views > 0) {
                Offer::where('id', $offer->id)->increment('views_count', $views);
            }
        }

        // ── Offres similaires — même catégorie, ordonnées par popularité
        $similarOffers = Offer::query()
            ->published()
            ->where('category', $offer->category)
            ->where('id', '!=', $offer->id)
            ->with(['city'])
            ->withAvg(['reviews' => fn ($q) => $q->where('status', 'published')], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('status', 'published')])
            ->orderByDesc('is_featured')
            ->orderByDesc('views_count')
            ->limit(3)
            ->get();

        // ── Disponibilité des modes de paiement
        $gatewayReady = config('services.fedapay.secret_key')
                     || config('services.stripe.secret');
        $hasOnline    = in_array($offer->payment_mode ?? 'on_site', ['online', 'both']) && $gatewayReady;
        $hasOnSite    = in_array($offer->payment_mode ?? 'on_site', ['on_site', 'both']);

        return view('pages.offers.show', compact(
            'offer', 'similarOffers', 'hasOnline', 'hasOnSite',
        ));
    }
}