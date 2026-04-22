<?php
declare(strict_types=1);
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole {
    public function handle(Request $request, Closure $next, string $role) {
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($request->user()->role !== $role) {
            return response()->json(['error' => 'Forbidden - Insufficient permissions'], 403);
        }

        return $next($request);
    }
}
