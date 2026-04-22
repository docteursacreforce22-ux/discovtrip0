<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * ════════════════════════════════════════════════════════════════════════════════
 * ADVANCED RATE LIMITING MIDDLEWARE
 * ════════════════════════════════════════════════════════════════════════════════
 * Protège contre:
 * - Attaques DDoS
 * - Brute force (login, register)
 * - API abuse
 * - Bot scraping
 * 
 * Stratégies:
 * - Par IP (global)
 * - Par user (authentifié)
 * - Par endpoint (spécifique)
 * ════════════════════════════════════════════════════════════════════════════════
 */
class AdvancedRateLimiting
{
    /**
     * Limites par endpoint (requests par minute)
     */
    private const ENDPOINT_LIMITS = [
        // Authentication - Strict
        'auth.login' => 5,           // 5 tentatives / 15 min
        'auth.register' => 3,        // 3 inscriptions / heure
        'auth.password.forgot' => 3, // 3 reset / heure
        'auth.password.reset' => 3,  // 3 reset / heure
        'auth.verify' => 5,          // 5 vérifications / 15 min
        
        // API - Modéré
        'api.search' => 30,          // 30 recherches / min
        'api.list' => 60,            // 60 listes / min
        'api.create' => 10,          // 10 créations / min
        'api.update' => 20,          // 20 updates / min
        'api.delete' => 10,          // 10 suppressions / min
        
        // Public - Permissif
        'public.read' => 100,        // 100 lectures / min
        'public.health' => 120,      // 120 health checks / min
    ];

    /**
     * Durée de blocage (en minutes)
     */
    private const DECAY_MINUTES = [
        'auth.login' => 15,
        'auth.register' => 60,
        'auth.password.forgot' => 60,
        'default' => 1,
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $limitKey = null): Response
    {
        // Déterminer la clé de rate limiting
        $key = $this->resolveRateLimitKey($request, $limitKey);
        
        // Récupérer la limite pour cet endpoint
        $limit = $this->getLimit($limitKey);
        $decayMinutes = $this->getDecayMinutes($limitKey);

        // Vérifier si limite atteinte
        if (RateLimiter::tooManyAttempts($key, $limit)) {
            return $this->buildRateLimitResponse($key, $limit);
        }

        // Incrémenter le compteur
        RateLimiter::hit($key, $decayMinutes * 60);

        // Continuer la requête
        $response = $next($request);

        // Ajouter headers rate limit info
        return $this->addRateLimitHeaders($response, $key, $limit);
    }

    /**
     * Résoudre la clé de rate limiting
     */
    private function resolveRateLimitKey(Request $request, ?string $limitKey): string
    {
        $keyParts = [];

        // Préfixe
        $keyParts[] = 'rate_limit';

        // Type de limite
        if ($limitKey) {
            $keyParts[] = $limitKey;
        }

        // Identifier l'utilisateur
        if ($user = $request->user()) {
            // Utilisateur authentifié → par user ID
            $keyParts[] = 'user';
            $keyParts[] = $user->id;
        } else {
            // Utilisateur non-auth → par IP
            $keyParts[] = 'ip';
            $keyParts[] = $request->ip();
        }

        return implode(':', $keyParts);
    }

    /**
     * Récupérer la limite pour un endpoint
     */
    private function getLimit(?string $limitKey): int
    {
        if ($limitKey && isset(self::ENDPOINT_LIMITS[$limitKey])) {
            return self::ENDPOINT_LIMITS[$limitKey];
        }

        // Limite par défaut selon authentification
        return request()->user() ? 60 : 10;
    }

    /**
     * Récupérer le decay time pour un endpoint
     */
    private function getDecayMinutes(?string $limitKey): int
    {
        if ($limitKey && isset(self::DECAY_MINUTES[$limitKey])) {
            return self::DECAY_MINUTES[$limitKey];
        }

        return self::DECAY_MINUTES['default'];
    }

    /**
     * Construire la réponse de rate limit atteint
     */
    private function buildRateLimitResponse(string $key, int $limit): Response
    {
        $retryAfter = RateLimiter::availableIn($key);

        return response()->json([
            'error' => 'Too Many Requests',
            'message' => 'Vous avez dépassé la limite de requêtes autorisées.',
            'limit' => $limit,
            'retry_after' => $retryAfter,
            'retry_after_human' => $this->formatRetryAfter($retryAfter),
        ], 429)
        ->header('Retry-After', $retryAfter)
        ->header('X-RateLimit-Limit', $limit)
        ->header('X-RateLimit-Remaining', 0);
    }

    /**
     * Ajouter headers d'information rate limit
     */
    private function addRateLimitHeaders(Response $response, string $key, int $limit): Response
    {
        $remaining = $limit - RateLimiter::attempts($key);
        $retryAfter = RateLimiter::availableIn($key);

        return $response
            ->header('X-RateLimit-Limit', $limit)
            ->header('X-RateLimit-Remaining', max(0, $remaining))
            ->header('X-RateLimit-Reset', now()->addSeconds($retryAfter)->timestamp);
    }

    /**
     * Formater le retry after en format lisible
     */
    private function formatRetryAfter(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' secondes';
        }

        $minutes = ceil($seconds / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    }

    /**
     * Méthode statique pour vérifier si IP est bloquée
     */
    public static function isBlocked(string $ip): bool
    {
        $key = "rate_limit:blocked:ip:{$ip}";
        return RateLimiter::tooManyAttempts($key, 0);
    }

    /**
     * Bloquer une IP manuellement
     */
    public static function blockIp(string $ip, int $minutes = 60): void
    {
        $key = "rate_limit:blocked:ip:{$ip}";
        RateLimiter::hit($key, $minutes * 60);
        
        // Log le blocage
        \Log::warning("IP blocked due to rate limiting", [
            'ip' => $ip,
            'duration' => $minutes,
            'timestamp' => now(),
        ]);
    }

    /**
     * Débloquer une IP manuellement
     */
    public static function unblockIp(string $ip): void
    {
        $key = "rate_limit:blocked:ip:{$ip}";
        RateLimiter::clear($key);
    }
}
