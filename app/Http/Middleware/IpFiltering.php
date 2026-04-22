<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ════════════════════════════════════════════════════════════════════════════════
 * IP FILTERING MIDDLEWARE
 * ════════════════════════════════════════════════════════════════════════════════
 * Contrôle l'accès basé sur l'adresse IP:
 * - Whitelist (autoriser seulement certaines IPs)
 * - Blacklist (bloquer certaines IPs)
 * ════════════════════════════════════════════════════════════════════════════════
 */
class IpFiltering
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip si filtrage IP désactivé
        if (!config('security.ip_filtering.enabled', false)) {
            return $next($request);
        }

        // Skip pour chemins exemptés
        if ($this->isExemptPath($request)) {
            return $next($request);
        }

        $clientIp = $this->getClientIp($request);

        // Vérifier blacklist d'abord
        if ($this->isBlacklisted($clientIp)) {
            return $this->denyAccess($clientIp, 'blacklisted');
        }

        // Si whitelist définie, vérifier si IP autorisée
        $whitelist = config('security.ip_filtering.whitelist', []);
        if (!empty($whitelist) && !$this->isWhitelisted($clientIp)) {
            return $this->denyAccess($clientIp, 'not_whitelisted');
        }

        return $next($request);
    }

    /**
     * Vérifier si le chemin est exempté
     */
    protected function isExemptPath(Request $request): bool
    {
        $exemptPaths = config('security.ip_filtering.exempt_paths', []);

        foreach ($exemptPaths as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Récupérer l'IP du client
     */
    protected function getClientIp(Request $request): string
    {
        return TrustProxies::getRealIp($request);
    }

    /**
     * Vérifier si IP est dans la blacklist
     */
    protected function isBlacklisted(string $ip): bool
    {
        $blacklist = config('security.ip_filtering.blacklist', []);

        foreach ($blacklist as $blockedIp) {
            if ($this->ipMatches($ip, $blockedIp)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifier si IP est dans la whitelist
     */
    protected function isWhitelisted(string $ip): bool
    {
        $whitelist = config('security.ip_filtering.whitelist', []);

        foreach ($whitelist as $allowedIp) {
            if ($this->ipMatches($ip, $allowedIp)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifier si IP correspond au pattern (supporte CIDR)
     */
    protected function ipMatches(string $ip, string $pattern): bool
    {
        // Exact match
        if ($ip === $pattern) {
            return true;
        }

        // CIDR notation (ex: 192.168.1.0/24)
        if (str_contains($pattern, '/')) {
            return $this->ipInRange($ip, $pattern);
        }

        // Wildcard (ex: 192.168.*.*)
        if (str_contains($pattern, '*')) {
            $regex = str_replace(
                ['*', '.'],
                ['[0-9]+', '\.'],
                $pattern
            );
            return (bool) preg_match("/^{$regex}$/", $ip);
        }

        return false;
    }

    /**
     * Vérifier si IP est dans un range CIDR
     */
    protected function ipInRange(string $ip, string $cidr): bool
    {
        [$subnet, $mask] = explode('/', $cidr);

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int) $mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }

    /**
     * Refuser l'accès
     */
    protected function denyAccess(string $ip, string $reason): Response
    {
        // Log l'événement
        \Log::warning('IP access denied', [
            'ip' => $ip,
            'reason' => $reason,
            'path' => request()->path(),
            'timestamp' => now(),
        ]);

        return response()->json([
            'error' => 'Access Denied',
            'message' => 'Your IP address is not authorized to access this resource.',
        ], 403);
    }

    /**
     * Ajouter IP à la blacklist dynamiquement
     */
    public static function blockIp(string $ip): void
    {
        // Cette méthode pourrait être utilisée pour bloquer une IP en runtime
        // Par exemple, après détection d'activité suspecte

        \Log::warning('IP dynamically blocked', [
            'ip' => $ip,
            'timestamp' => now(),
        ]);

        // Dans une vraie implémentation, on stockerait en cache/DB
        // Pour l'instant, on log seulement
    }
}
