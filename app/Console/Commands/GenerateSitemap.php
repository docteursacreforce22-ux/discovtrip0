<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Offer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap extends Command
{
    protected $signature   = 'sitemap:generate {--force : Régénérer même si le cache est frais}';
    protected $description = 'Génère le sitemap XML et le met en cache';

    public function handle(): int
    {
        $this->info('🗺️  Génération du sitemap...');

        // Vider le cache existant
        Cache::forget('sitemap_xml');

        try {
            $xml = $this->buildSitemap();

            // Mettre en cache 24h
            Cache::put('sitemap_xml', $xml, 86400);

            // Écrire aussi un fichier statique dans public/ pour les crawlers
            // (fallback si le cache Laravel est vide)
            $publicPath = public_path('sitemap.xml');
            file_put_contents($publicPath, $xml);

            $urlCount = substr_count($xml, '<url>');
            $this->info("✅ Sitemap généré : {$urlCount} URLs. Fichier : public/sitemap.xml");

        } catch (\Throwable $e) {
            $this->error('❌ Erreur : ' . $e->getMessage());
            \Log::error('GenerateSitemap failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function buildSitemap(): string
    {
        $appUrl = rtrim(config('app.url'), '/');
        $now    = now()->toAtomString();

        $urls = collect();

        // ── Pages statiques ───────────────────────────────────────────────
        $staticPages = [
            ['url' => '/',              'priority' => '1.0', 'freq' => 'daily'],
            ['url' => '/offers',        'priority' => '0.9', 'freq' => 'daily'],
            ['url' => '/destinations',  'priority' => '0.8', 'freq' => 'weekly'],
            ['url' => '/about',         'priority' => '0.6', 'freq' => 'monthly'],
            ['url' => '/contact',       'priority' => '0.5', 'freq' => 'monthly'],
            ['url' => '/faq',           'priority' => '0.5', 'freq' => 'monthly'],
        ];

        foreach ($staticPages as $page) {
            $urls->push($this->urlEntry(
                $appUrl . $page['url'],
                $now,
                $page['freq'],
                $page['priority']
            ));
        }

        // ── Offres publiées ────────────────────────────────────────────────
        $offers = Offer::published()
            ->select(['slug', 'updated_at'])
            ->get();

        foreach ($offers as $offer) {
            $urls->push($this->urlEntry(
                $appUrl . '/offers/' . $offer->slug,
                $offer->updated_at->toAtomString(),
                'daily',
                '0.9'
            ));
        }

        // ── Villes actives ─────────────────────────────────────────────────
        $cities = City::active()
            ->whereNotNull('slug')
            ->select(['slug', 'updated_at'])
            ->get();

        foreach ($cities as $city) {
            $urls->push($this->urlEntry(
                $appUrl . '/destinations/' . $city->slug,
                $city->updated_at->toAtomString(),
                'weekly',
                '0.8'
            ));
        }

        // ── Assembler le XML ───────────────────────────────────────────────
        $urlsXml = $urls->implode('');

        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n"
            . $urlsXml
            . '</urlset>';
    }

    private function urlEntry(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        return "  <url>\n"
            . "    <loc>" . htmlspecialchars($loc, ENT_XML1 | ENT_COMPAT, 'UTF-8') . "</loc>\n"
            . "    <lastmod>{$lastmod}</lastmod>\n"
            . "    <changefreq>{$changefreq}</changefreq>\n"
            . "    <priority>{$priority}</priority>\n"
            . "  </url>\n";
    }
}