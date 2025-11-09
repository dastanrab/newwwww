<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureJsonRequest
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->expectsJson()) {
            return response()->json([
                'message' => 'Only RESTful JSON requests are allowed'
            ], 403);
        }

        return $next($request);
    }
}
