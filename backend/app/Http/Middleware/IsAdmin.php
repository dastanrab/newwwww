<?php

namespace App\Http\Middleware;

use App\Events\ActivityEvent;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
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
            if($user->getRoles()->intersect(Role::AccessToDashboard())->count()){
                return $next($request);
            }
            elseif ($request->is('activity*')) {
                event(new ActivityEvent("دسترسی به لاگ ندارد", 'activities', false));
            }else{
                auth()->logout();
                return redirect(route('d.login'));
            }
        }
        else{
            return redirect(route('d.login'));
        }

    }
}
