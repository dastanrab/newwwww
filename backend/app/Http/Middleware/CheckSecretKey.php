<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSecretKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = $request->header('X-Secret-Key');
        $validSecret = 'salman'; // or hardcode for testing

        if (!$secret || $secret !== $validSecret) {
            return response()->json(['status'=>401,'result'=>'','message'=>'دسترسی شما احراز نیست']);
        }

        return $next($request);
    }
}
