<?php

namespace App\Http\Middleware;

use App\Models\Car;
use App\Models\Location;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasTodayLoaction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $car = Car::query()->where('user_id',$user->id)->first();
        $location=Location::query()->where('car_id',$car->id)->whereDate('created_at',now())->latest()->first();
        if($location){
            return $next($request);
        }
        else{
            return sendJson('error','شما امروز موقعیت ثبت نکرده اید');
        }
    }
}
