<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeliveryAgentMiddleware
{
    /**
     * Handle an incoming request.
     * Only users with 'delivery_agent' or 'admin' role may pass.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (auth()->user()->role === 'delivery_agent' || auth()->user()->role === 'admin')) {
            return $next($request);
        }
        return response()->json(['error' => 'Forbidden. Only Delivery Agents and Admins can access this resource.'], 403);
    }
}
