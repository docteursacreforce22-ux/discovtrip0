<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Offer;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function show(string $slug, Request $request)
    {
        // Ville avec toutes ses métadonnées
        $city = City::where('slug', $slug)
            ->where('is_active', true)
            ->withCount(['offers' => fn ($q) => $q->where('status', 'published')])
            ->withMin(
                ['offers as offers_min_base_price' => fn ($q) => $q->where('status', 'published')],
                'base_price'
            )
            ->firstOrFail();
        // Note : $city->average_rating est lu depuis la colonne directe du modèle.
        // Le withAvg était redondant et a été retiré.

        // Offres de la ville — filtrables, triables, paginées
        $offersQuery = Offer::where('city_id', $city->id)
            ->where('status', 'published')
            ->with(['city', 'reviews'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        // Filtre catégorie
        if ($request->filled('category')) {
            $offersQuery->where('category', $request->category);
        }

        // Tri
        match ($request->get('sort', 'featured')) {
            'price_asc'  => $offersQuery->orderBy('base_price', 'asc'),
            'price_desc' => $offersQuery->orderBy('base_price', 'desc'),
            'rating'     => $offersQuery->orderByDesc('reviews_avg_rating'),
            default      => $offersQuery->orderByDesc('is_featured')->orderByDesc('created_at'),
        };

        $offers = $offersQuery->paginate(9)->withQueryString();

        // Catégories disponibles pour cette ville
        $categories = Offer::where('city_id', $city->id)
            ->where('status', 'published')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();

        // Villes proches — même pays, actives, différentes
        $nearbyCities = City::where('id', '!=', $city->id)
            ->where('is_active', true)
            ->where('country_id', $city->country_id)
            ->withCount(['offers' => fn ($q) => $q->where('status', 'published')])
            ->withMin(
                ['offers as offers_min_base_price' => fn ($q) => $q->where('status', 'published')],
                'base_price'
            )
            ->orderByDesc('is_featured')
            ->limit(4)
            ->get();

        // Offre hero — featured avec image en priorité, sinon première avec image
        $heroOffer = Offer::where('city_id', $city->id)
            ->where('status', 'published')
            ->whereNotNull('cover_image')
            ->where('is_featured', true)
            ->first()
            ?? Offer::where('city_id', $city->id)
                ->where('status', 'published')
                ->whereNotNull('cover_image')
                ->first();

        return view('pages.city', compact(
            'city',
            'offers',
            'categories',
            'nearbyCities',
            'heroOffer',
        ));
    }
}