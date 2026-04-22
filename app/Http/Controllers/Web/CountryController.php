<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Offer;

class CountryController extends Controller
{
    /**
     * Page détail d'un pays — liste de ses villes
     * URL : /destinations/{country_slug}
     */
    public function show(string $slug)
    {
        $country = Country::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $featuredCities = City::where('country_id', $country->id)
            ->where('is_featured', true)
            ->where('is_active', true)
            ->withCount(['offers' => fn($q) => $q->where('status', 'published')])
            ->withMin(
                ['offers as offers_min_base_price' => fn($q) => $q->where('status', 'published')],
                'base_price'
            )
            ->orderBy('featured_order')
            ->orderBy('name')
            ->get();

        $cities = City::where('country_id', $country->id)
            ->where('is_active', true)
            ->withCount(['offers' => fn($q) => $q->where('status', 'published')])
            ->withMin(
                ['offers as offers_min_base_price' => fn($q) => $q->where('status', 'published')],
                'base_price'
            )
            ->orderByDesc('is_featured')
            ->orderBy('featured_order')
            ->orderBy('name')
            ->paginate(12);

        $heroCards = $cities->getCollection()->take(2);
        $gridCards = $cities->getCollection()->skip(2)->values();

        $totalOffers = Offer::whereHas('city', fn($q) => $q->where('country_id', $country->id))
            ->where('status', 'published')
            ->count();

        return view('pages.country', compact(
            'country', 'featuredCities', 'cities',
            'heroCards', 'gridCards', 'totalOffers',
        ));
    }
}
