<?php

namespace App\Http\Middleware;

use App\Events\ActivityEvent;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $isLogged = auth()->check();
        if($isLogged){
            $user = auth()->user();
            if($user->getRoles()->intersect(Role::AccessToDashboard())->count()){
                return $next($request);
            }
        }
        $serverIp = '45.159.115.40';
        $clientIp = $request->ip();
        if ($clientIp !== $serverIp) {
            return response()->json(['message' => 'access denied!!'], 403);
        }
        return $next($request);
    }
}
