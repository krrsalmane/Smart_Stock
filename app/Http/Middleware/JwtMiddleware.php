<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Try to get token from authorization header first
            $token = $request->bearerToken();
            
            // If not in header, try query parameter (for exports/redirects)
            if (!$token && $request->has('token')) {
                JWTAuth::setToken($request->query('token'))->authenticate();
            } else if ($token) {
                JWTAuth::parseToken()->authenticate();
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Unauthorized: ' . $e->getMessage()], 401);
        }

        return $next($request);
    }
}
