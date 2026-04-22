<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Offer;
use App\Models\SiteSetting;
use App\Models\Spotlight;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    // ════════════════════════════════════════════════════════
    // PAGE D'ACCUEIL
    // ════════════════════════════════════════════════════════

    public function index()
    {
        // ── 1. Offres vedettes — cache 10 min
        $featuredOffers = Cache::remember('home.featured_offers', 600, function () {
            try {
                // Cascade : featured publiées → toutes publiées → toutes
                $offers = Offer::query()
                    ->where('is_featured', true)
                    ->published()
                    ->with(['city'])
                    ->withCount('reviews')
                    ->withAvg('reviews', 'rating')
                    ->orderByDesc('published_at')
                    ->limit(5)
                    ->get();

                if ($offers->isEmpty()) {
                    $offers = Offer::query()
                        ->published()
                        ->with(['city'])
                        ->withCount('reviews')
                        ->withAvg('reviews', 'rating')
                        ->orderByDesc('created_at')
                        ->limit(5)
                        ->get();
                }

                if ($offers->isEmpty()) {
                    $offers = Offer::query()
                        ->with(['city'])
                        ->orderByDesc('created_at')
                        ->limit(5)
                        ->get();
                }

                return $offers;
            } catch (\Throwable $e) {
                Log::error('HomeController.featured_offers: ' . $e->getMessage());
                return collect();
            }
        });

        // ── 2. Villes vedettes — cache 10 min
        $featuredCities = Cache::remember('home.featured_cities', 600, function () {
            try {
                $cities = City::query()
                    ->where('is_featured', true)
                    ->where('is_active', true)
                    ->withCount(['offers' => fn ($q) => $q->published()])
                    ->orderByDesc('offers_count')
                    ->limit(5)
                    ->get();

                if ($cities->isEmpty()) {
                    $cities = City::query()
                        ->where('is_active', true)
                        ->withCount(['offers' => fn ($q) => $q->published()])
                        ->orderByDesc('offers_count')
                        ->limit(5)
                        ->get();
                }

                if ($cities->isEmpty()) {
                    $cities = City::query()
                        ->withCount('offers')
                        ->orderBy('name')
                        ->limit(5)
                        ->get();
                }

                return $cities;
            } catch (\Throwable $e) {
                Log::error('HomeController.featured_cities: ' . $e->getMessage());
                return collect();
            }
        });

        // ── 3. Spotlight — cache 5 min
        $spotlight = Cache::remember('home.spotlight', 300, function () {
            try {
                return Spotlight::query()
                    ->where('is_active', true)
                    ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                    ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
                    ->orderBy('order')
                    ->first();
            } catch (\Throwable $e) {
                Log::warning('HomeController.spotlight: ' . $e->getMessage());
                return null;
            }
        });

        // ── 4. Témoignages — cache 30 min (changent rarement)
        $testimonials = Cache::remember('home.testimonials', 1800, function () {
            try {
                return Testimonial::query()
                    ->where('is_published', true)
                    ->orderBy('order')
                    ->limit(3)
                    ->get();
            } catch (\Throwable $e) {
                Log::warning('HomeController.testimonials: ' . $e->getMessage());
                return collect();
            }
        });

        // ── 5. Stats globales — cache 5 min
        [$totalOffers, $totalCities] = Cache::remember('home.stats', 300, function () {
            try {
                return [
                    Offer::published()->count(),
                    City::where('is_active', true)->count(),
                ];
            } catch (\Throwable $e) {
                Log::error('HomeController.stats: ' . $e->getMessage());
                return [50, 8];
            }
        });

        return view('pages.home', [
            'featuredOffers' => $featuredOffers,
            'featuredCities' => $featuredCities,
            'spotlight'      => $spotlight,
            'testimonials'   => $testimonials,
            'totalOffers'    => $totalOffers,
            'totalCities'    => $totalCities,
        ]);
    }

    // ════════════════════════════════════════════════════════
    // RECHERCHE RAPIDE
    // ════════════════════════════════════════════════════════

    public function search(Request $request)
    {
        return redirect()->route('offers.index', $request->only(['city', 'category', 'q']));
    }

    // ════════════════════════════════════════════════════════
    // NEWSLETTER — Inscription
    // ════════════════════════════════════════════════════════

    public function subscribeNewsletter(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:120'],
        ]);

        $email = strtolower(trim($request->email));

        try {
            // Vérifier si déjà inscrit
            $existing = DB::table('newsletter_subscribers')
                ->where('email', $email)
                ->first();

            if ($existing) {
                return $this->newsletterResponse($request, 'Vous êtes déjà inscrit à notre newsletter.');
            }

            // Enregistrer en base
            DB::table('newsletter_subscribers')->insert([
                'email'      => $email,
                'ip'         => $request->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Newsletter subscription: ' . $email);

        } catch (\Throwable $e) {
            // Si la table n'existe pas encore, juste logger (pas d'erreur visible)
            Log::warning('Newsletter DB save failed: ' . $e->getMessage());
            Log::info('Newsletter subscription (fallback): ' . $email);
        }

        return $this->newsletterResponse($request, 'Merci ! Vous êtes inscrit à notre newsletter. 🎉');
    }

    // ─────────────────────────────────────────────────────────
    // Helper : répondre JSON ou redirect selon le type de requête
    // ─────────────────────────────────────────────────────────

    private function newsletterResponse(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return back()->with('newsletter_success', $message);
    }
}