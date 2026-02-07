<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfIos
{
    public function handle(Request $request, Closure $next)
{
    $userAgent = $request->header('User-Agent');
    if (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
        $request->attributes->set('is_ios', true);
    } else {
        $request->attributes->set('is_ios', false);
    }

    return $next($request);
}
}
