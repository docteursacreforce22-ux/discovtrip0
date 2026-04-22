<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ════════════════════════════════════════════════════════════════════════════════
 * SECURITY HEADERS MIDDLEWARE
 * ════════════════════════════════════════════════════════════════════════════════
 * Ajoute des headers de sécurité HTTP pour protéger contre:
 * - XSS (Cross-Site Scripting)
 * - Clickjacking
 * - MIME-type sniffing
 * - Man-in-the-middle attacks
 * - Information leakage
 * ════════════════════════════════════════════════════════════════════════════════
 */
class SecurityHeaders
{
    /**
     * Headers de sécurité à appliquer
     */
    private const SECURITY_HEADERS = [
        // Protection XSS
        'X-XSS-Protection' => '1; mode=block',
        
        // Empêche le MIME-type sniffing
        'X-Content-Type-Options' => 'nosniff',
        
        // Protection Clickjacking
        'X-Frame-Options' => 'DENY',
        
        // Politique de référent
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        
        // Permissions navigateur
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Appliquer headers de sécurité basiques
        foreach (self::SECURITY_HEADERS as $header => $value) {
            $response->headers->set($header, $value, false);
        }

        // Content Security Policy (CSP)
        if (config('security.csp.enabled', true)) {
            $response->headers->set(
                'Content-Security-Policy',
                $this->buildCspHeader(),
                false
            );
        }

        // Strict-Transport-Security (HSTS) - Seulement en HTTPS
        if ($request->secure() && config('security.hsts.enabled', true)) {
            $maxAge = config('security.hsts.max_age', 31536000); // 1 an par défaut
            $includeSubDomains = config('security.hsts.include_subdomains', true);
            $preload = config('security.hsts.preload', false);

            $hstsValue = "max-age={$maxAge}";
            if ($includeSubDomains) {
                $hstsValue .= '; includeSubDomains';
            }
            if ($preload) {
                $hstsValue .= '; preload';
            }

            $response->headers->set('Strict-Transport-Security', $hstsValue, false);
        }

        // Retirer headers qui révèlent des informations
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }

    /**
     * Construire le header Content-Security-Policy
     */
    private function buildCspHeader(): string
    {
        $cspDirectives = config('security.csp.directives', [
            "default-src" => ["'self'"],
            "script-src" => ["'self'", "'unsafe-inline'"],
            "style-src" => ["'self'", "'unsafe-inline'"],
            "img-src" => ["'self'", "data:", "https:"],
            "font-src" => ["'self'", "data:"],
            "connect-src" => ["'self'"],
            "frame-ancestors" => ["'none'"],
            "base-uri" => ["'self'"],
            "form-action" => ["'self'"],
        ]);

        $csp = [];
        foreach ($cspDirectives as $directive => $sources) {
            $csp[] = $directive . ' ' . implode(' ', $sources);
        }

        return implode('; ', $csp);
    }

    /**
     * Déterminer si la requête nécessite une protection CSRF
     */
    private function shouldEnforceCsrf(Request $request): bool
    {
        // Exemptions CSRF (ex: API endpoints avec JWT)
        $exemptPaths = config('security.csrf.exempt', [
            'api/*',
        ]);

        foreach ($exemptPaths as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        return true;
    }
}
