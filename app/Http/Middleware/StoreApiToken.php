<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StoreApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $configured = (string) config('store_api.token');
        $given = (string) $request->bearerToken();

        if ($configured === '' || ! hash_equals($configured, $given)) {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        return $next($request);
    }
}
