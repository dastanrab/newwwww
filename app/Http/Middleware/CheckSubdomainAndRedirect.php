<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubdomainAndRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()){
            $subdomain = explode('.', $request->getHost())[0];
            if ($request->is('login') && $subdomain == 'club') {
                return redirect(route('cl.dashboard'));
            }
            elseif ($request->is('pa/login') && $subdomain == 'dashboard'){
                return redirect(route('d.home'));
            }
        }
        return $next($request);
    }
}
