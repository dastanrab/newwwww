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
        '185.255.88.111',
        '172.18.0.5'
    ];

    public function handle(Request $request, Closure $next)
    {
        $realIp = @$request->header('x-real-ip');
        $ip = $request->ip();
        if (!in_array($ip, $this->allowed)) {
            return response()->json([
                'message' => 'Your IP is not allowed to access this resource.'
            ], 403);
        }

        return $next($request);
    }
}
