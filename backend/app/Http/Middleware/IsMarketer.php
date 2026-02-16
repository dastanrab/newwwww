<?php

namespace App\Http\Middleware;

use App\Events\ActivityEvent;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsMarketer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isLogged = auth()->check();
        if($isLogged){
            $user = auth()->user();
            if($user->getRoles()->intersect(['superadmin','marketer'])->count()){
                return $next($request);
            }
            elseif ($request->is('activity*')) {
                event(new ActivityEvent("دسترسی به لاگ ندارد", 'activities', false));
            }else{
                return redirect()->to(env('SITE_URL'));
            }
        }
        else{
            return redirect(route('cl.login'));
        }
    }
}
