<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.force_https')) {
            return $next($request);
        }

        if ($request->isSecure() || $request->header('X-Forwarded-Proto') === 'https') {
            return $next($request);
        }

        return response()->json([
            'message' => 'HTTPS is required for all API requests.',
        ], 426);
    }
}
