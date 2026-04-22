<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $this->detectLocale($request);

        App::setLocale($locale);

        $response = $next($request);

        // Persister en session Laravel
        session(['locale' => $locale]);

        // Cookie locale — secure=true uniquement en production (HTTPS)
        $secure = app()->isProduction();
        $response->cookie('locale', $locale, 60 * 24 * 365, '/', null, $secure, true);

        return $response;
    }

    protected function detectLocale(Request $request): string
    {
        $supported = array_keys(array_filter(
            config('locales.supported', []),
            fn ($lang) => $lang['enabled'] ?? false
        ));

        // Fallback si config vide
        if (empty($supported)) {
            $supported = ['fr', 'en'];
        }

        // 1. Cookie
        if ($cookie = $request->cookie('locale')) {
            if (in_array($cookie, $supported, true)) return $cookie;
        }

        // 2. Session
        if ($session = session('locale')) {
            if (in_array($session, $supported, true)) return $session;
        }

        // 3. Paramètre URL ?lang=fr ou ?locale=fr
        $param = $request->query('lang') ?? $request->query('locale');
        if ($param && in_array($param, $supported, true)) {
            return $param;
        }

        // 4. Header Accept-Language du navigateur
        if ($header = $request->getPreferredLanguage($supported)) {
            return $header;
        }

        // 5. Défaut config
        return config('app.locale', 'fr');
    }
}