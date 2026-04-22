<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\City;
use App\Models\Country;
use App\Models\Offer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Sitemap XML principal — mis en cache 6h.
     * Invalidé automatiquement si tu veux via :
     *   Cache::forget('sitemap_xml');
     */
    public function index(): Response
    {
        $xml = Cache::remember('sitemap_xml', 21600, fn() => $this->buildXml());

        return response($xml, 200, [
            'Content-Type'  => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=21600',
        ]);
    }

    private function buildXml(): string
    {
        $base = rtrim(config('app.url'), '/');
        $now  = now()->toAtomString();
        $urls = [];

        // ── Pages statiques ────────────────────────────────
        foreach ([
            ['/',                       '1.0', 'daily'],
            ['/destinations',           '0.9', 'weekly'],
            ['/offers',                 '0.9', 'daily'],
            ['/blog',                   '0.8', 'weekly'],
            ['/about',                  '0.6', 'monthly'],
            ['/contact',                '0.6', 'monthly'],
            ['/faq',                    '0.7', 'monthly'],
            ['/conditions-utilisation', '0.3', 'yearly'],
            ['/confidentialite',        '0.3', 'yearly'],
            ['/annulation-gratuite',    '0.5', 'yearly'],
        ] as [$loc, $pri, $freq]) {
            $urls[] = ['loc' => $base.$loc, 'lastmod' => $now, 'changefreq' => $freq, 'priority' => $pri];
        }

        // ── Pays actifs (mode multi-pays futur) ────────────
        Country::active()
            ->has('activeCities')
            ->select(['slug', 'updated_at'])
            ->orderBy('name')
            ->get()
            ->each(function ($country) use (&$urls, $base) {
                $urls[] = [
                    'loc'        => $base . '/destinations/country/' . $country->slug,
                    'lastmod'    => $country->updated_at?->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority'   => '0.85',
                ];
            });

        // ── Villes actives ─────────────────────────────────
        City::where('is_active', true)
            ->whereNotNull('slug')
            ->select(['slug', 'updated_at'])
            ->orderBy('name')
            ->get()
            ->each(function ($city) use (&$urls, $base) {
                $urls[] = [
                    'loc'        => $base . '/destinations/' . $city->slug,
                    'lastmod'    => $city->updated_at?->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority'   => '0.8',
                ];
            });

        // ── Offres publiées ────────────────────────────────
        Offer::where('status', 'published')
            ->whereNull('deleted_at')
            ->whereNotNull('slug')
            ->select(['slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->get()
            ->each(function ($offer) use (&$urls, $base) {
                $urls[] = [
                    'loc'        => $base . '/offers/' . $offer->slug,
                    'lastmod'    => $offer->updated_at?->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority'   => '0.9',
                ];
            });

        // ── Articles de blog publiés ───────────────────────
        BlogPost::published()
            ->whereNotNull('slug')
            ->select(['slug', 'updated_at'])
            ->orderByDesc('published_at')
            ->get()
            ->each(function ($post) use (&$urls, $base) {
                $urls[] = [
                    'loc'        => $base . '/blog/' . $post->slug,
                    'lastmod'    => $post->updated_at?->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority'   => '0.7',
                ];
            });

        // ── Génération XML ──────────────────────────────────
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
        $xml .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n";
        $xml .= '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>"        . htmlspecialchars($u['loc'], ENT_XML1)  . "</loc>\n";
            $xml .= "    <lastmod>"    . $u['lastmod']                          . "</lastmod>\n";
            $xml .= "    <changefreq>" . $u['changefreq']                       . "</changefreq>\n";
            $xml .= "    <priority>"   . $u['priority']                         . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= "</urlset>\n";

        return $xml;
    }
}
