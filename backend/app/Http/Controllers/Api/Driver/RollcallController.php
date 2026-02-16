<?php

namespace App\Http\Controllers\Api\Driver;

use App\Classes\BaleService;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Location;
use App\Models\Rollcall;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RollcallController extends Controller
{
    public function rollCall(Request $request)
    {
        $user = auth()->user();
        $bale=new BaleService();
        $bale->raw_msg([$request->toArray(),$user->id]);
        $rollcall = $user->rollcalls()->whereDate('start_at',now())->where('end_at','=',null)->first();
        $limiter = $user->id == 2 ? 100 : 2;
        if (isset($request->lat) and isset($request->lng))
        {
                $car = Car::where('user_id', $user->id)->where('is_active', true)->first();
                $location = new Location;
                $location->car_id = $car->id;
                $location->lat = $request->lat;
                $location->long = $request->lng;
                $location->date = now();
                $location->save();
        }
        if($user->rollcalls()->whereDate('start_at',now())->where('end_at','!=',null)->count() >= $limiter){
            return sendJson('error','امکان ثبت حضور بیشتر ۲ بار در روز فراهم نمی باشد', $user->rollCallData());
        }
        elseif (!isLocationInsidePolygon($user->id,[$request->lat, $request->lng])){
            if($rollcall) {
                $user->failedRollcall()->create([
                    'end_lat' => $request->lat,
                    'end_lon' => $request->lng,
                ]);
            }
            else{
                $user->failedRollcall()->create([
                    'start_lat' => $request->lat,
                    'start_lon' => $request->lng,
                ]);
            }
            return sendJson('error','شما خارج از منطقه می باشید', $user->rollCallData());
        }

        if($rollcall){ // یعنی حضور داشته و الان باید پایان حضور ثبت بشه
            $rollcall->update(['end_at' => now(), 'end_lat' => $request->lat, 'end_lon' => $request->lng]);
            $car = Car::where('user_id', $user->id)->where('is_active', true)->first();
            $car->update(['rollcall_status' => 1]);
            return sendJson('success','حضور شما پایان یافت', $user->rollCallData());
        }
        else{
            $rollcall = new Rollcall;
            $rollcall->user_id = $user->id;
            $rollcall->start_lat = $request->lat;
            $rollcall->start_lon = $request->lng;
            $rollcall->start_at = now();
            $rollcall->save();
            $car = Car::where('user_id', $user->id)->where('is_active', true)->first();
            $car->update(['rollcall_status' => 2]);
            return sendJson('success','حضور شما ثبت شد', $user->rollCallData());
        }
    }
}
