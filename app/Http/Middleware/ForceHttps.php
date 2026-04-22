<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ════════════════════════════════════════════════════════════════════════════════
 * FORCE HTTPS MIDDLEWARE
 * ════════════════════════════════════════════════════════════════════════════════
 * Force l'utilisation de HTTPS en production
 * Redirige automatiquement HTTP → HTTPS
 * ════════════════════════════════════════════════════════════════════════════════
 */
class ForceHttps
{
    /**
     * Chemins exemptés (ex: health checks)
     */
    protected array $exempt = [
        'api/health',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip en développement local
        if (app()->environment('local')) {
            return $next($request);
        }

        // Skip si HTTPS forcé désactivé
        if (!config('security.api.force_https', true)) {
            return $next($request);
        }

        // Skip pour chemins exemptés
        if ($this->shouldExempt($request)) {
            return $next($request);
        }

        // Vérifier si la requête est déjà en HTTPS
        if (!$request->secure()) {
            // Rediriger vers HTTPS
            return redirect()->secure(
                $request->getRequestUri(),
                301 // Permanent redirect
            );
        }

        return $next($request);
    }

    /**
     * Déterminer si la requête doit être exemptée
     */
    protected function shouldExempt(Request $request): bool
    {
        foreach ($this->exempt as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}
