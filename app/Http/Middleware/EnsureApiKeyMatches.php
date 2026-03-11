<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiKeyMatches
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredApiKey = (string) config('services.frontend.api_key');

        if ($configuredApiKey === '') {
            return $next($request);
        }

        $requestApiKey = (string) $request->header('X-API-KEY');

        if ($requestApiKey === '' || ! hash_equals($configuredApiKey, $requestApiKey)) {
            return response()->json([
                'message' => 'The provided API key is invalid.',
            ], 401);
        }

        return $next($request);
    }
}
