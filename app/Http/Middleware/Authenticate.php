<?php
declare(strict_types=1);
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class Authenticate {
    public function handle(Request $request, Closure $next) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            if (!$user->is_active || $user->is_banned) {
                return response()->json(['error' => 'Account not active'], 403);
            }

            $request->setUserResolver(fn() => $user);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
