<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;

class IsDriver
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        if($user->getRole('name') == 'driver'){
            return $next($request);
        }
        if($request->header('content-type') !== null && $request->header('content-type') == 'application/json') {
            return sendJson('error','خطا در احراز توکن');
        }
        return redirect('/');

    }

}
