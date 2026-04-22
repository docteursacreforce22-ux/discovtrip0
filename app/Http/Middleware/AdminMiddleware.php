<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ═══════════════════════════════════════════════════════════════════════════════
 * ADMIN MIDDLEWARE - Vérification accès administrateur (compatible Laravel 12)
 * ═══════════════════════════════════════════════════════════════════════════════
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }

        if ($user->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Accès refusé'], 403);
            }

            abort(403, 'Accès administrateur requis');
        }

        return $next($request);
    }
}
