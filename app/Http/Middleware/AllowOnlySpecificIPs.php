<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowOnlySpecificIPs
{
    // List of allowed IP addresses
    protected array $allowed = [
        '127.0.0.1',
        '192.168.1.50',
        '10.0.0.12',
    ];

    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        dd($request->ip(),$request->headers,$request);
        if (!in_array($ip, $this->allowed)) {
            return response()->json([
                'message' => 'Your IP is not allowed to access this resource.'
            ], 403);
        }

        return $next($request);
    }
}
