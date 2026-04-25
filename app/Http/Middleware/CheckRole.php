<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckRole — Vérifie que l'utilisateur a le rôle requis.
 *
 * CORRECTION : retourne maintenant une redirection HTTP pour les
 * requêtes web (au lieu de JSON systématique), conformément à la
 * convention Laravel. JSON uniquement pour les requêtes API/XHR.
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // ── Non authentifié ───────────────────────────────────────
        if (! $request->user()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // ── Rôle insuffisant ──────────────────────────────────────
        if ($request->user()->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error'    => 'Forbidden',
                    'message'  => 'Insufficient permissions',
                    'required' => $role,
                ], 403);
            }

            abort(403, 'Accès refusé — rôle requis : ' . $role);
        }

        return $next($request);
    }
}
