<?php
declare(strict_types=1);
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RateLimiting {
    public function handle(Request $request, Closure $next, string $limit = '60') {
        $key = $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, (int) $limit)) {
            return response()->json([
                'error' => 'Too many requests',
                'retry_after' => RateLimiter::availableIn($key),
            ], 429);
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}
