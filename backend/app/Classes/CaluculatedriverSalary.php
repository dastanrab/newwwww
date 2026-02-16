<?php

namespace App\Classes;

use App\Models\Address;
use App\Models\Driver;
use App\Models\DriversAttendanceLogs;
use App\Models\DriversSalaryDetails;
use App\Models\DriverSuggestedRequests;
use App\Models\QueueFails;
use App\Models\Receive;
use App\Models\Submit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CaluculatedriverSalary
{
    private  $driver;
    private $drivers;
    private $date;
    private $bale;

    /**
     * Create a new job instance.
     */
    public function __construct($driver,$date)
    {
        $this->bale= new BaleService();
        $this->driver = $driver;
        $this->date=$date;
        $this->submits=DriverSuggestedRequests::query()->where('driver_id',$this->driver->id)->whereDate('start_at',$this->date)->where('status',2)->orderBy('id','asc')->get()->pluck('submit_id')->toArray();
    }
    private function in_region($start,$end)
    {
        if (isset($start))
        {
            $start=DriverSuggestedRequests::query()->where('submit_id',$start)->where('driver_id',$this->driver->id)->whereDate('created_at',$this->date)->where('status',2)->first();
        }
        $end=DriverSuggestedRequests::query()->where('submit_id',$end)->where('driver_id',$this->driver->id)->whereDate('created_at',$this->date)->where('status',2)->first();
        if ( isset($end)  && $end->in_regions) {
            return true;
        }
        return false;

    }

    /**
     * Execute the job.
     */
    public function salary()
    {
//        $log = fopen("/home/laravel/la.bazistco.com/storage/logs/DriverSalaryLog.txt", "a+") or die("Unable to open file!");
//        $t = 'res|'.json_encode($this->driver->id)."\n";
//        fwrite($log, $t);
                if (count($this->submits)>0)
                {
                    $start=null;
                    foreach ($this->submits as $submit)
                    {
//                        $exist=DriversSalaryDetails::query()->where('submit_id',$submit)->where('user_id',$this->driver->id)->first();
                        if (true)
                        {
                            try {
                                DB::beginTransaction();
                                $weights=$this->getWeights($submit);
                                if ($this->in_region($start,$submit))
                                {
                                    $distance=$this->getDistance($start,$submit);
                                }
                                else{
                                    $this->bale->distanceLog('درمنطقه نیست',['start'=>$start,'submit'=>$start,'end'=>$submit]);

                                    $distance=0;
                                }
                                $start=$submit;
                                dump($start,$distance);
                                $submit_user=Submit::query()->where('id',$submit)->first()->user_id;
                                $static=false;
                                if ($submit == 304998 and $this->driver->id ==89595)
                                {
                                    $static =true;
                                }
                                $total_attendance=$this->getTotalAttendance();
                                $reward=$this->getReward($weights,$distance,$static);
                                $weight_price=($reward>0 and $weights>0)?$reward/$weights:0;
                                DriversSalaryDetails::query()->insert(['submit_id'=>$submit,'user_id'=>$this->driver->id,'distance'=>$distance,'total_attendance'=>$total_attendance,'metals_reward'=>0,'weight_price'=>$weight_price,'reward_price'=>$reward,'weight'=>$weights,'creator_id'=>0,'created_at'=>"{$this->date} 00:00:00",'updated_at'=>"{$this->date} 00:00:00"]);
                                DB::commit();
//                                sleep(1);

                            }catch (\Exception $exception)
                            {
                                DB::rollBack();
                              ////  $log = fopen("/home/laravel/la.bazistco.com/storage/logs/InsertSalaryErrorLog.txt", "a+") or die("Unable to open file!");
                               // $t = 'res|'.json_encode($exception->getMessage())."\n";
                                //fwrite($log, $t);
                                QueueFails::query()->create(['queue'=>class_basename($this),'data'=>['driver_id'=>$this->driver->id,'date'=>$this->date,'error'=>$exception->getMessage()]]);

                            }
                        }
                    }

                }
                return 'ok';


    }
    private function getDistance($start,$submit)
    {
        if (!isset($start))
        {
            $this->bale->distanceLog('عدم وجود درخواست شروع',['start'=>@$start,'end'=>@$submit]);
            return 0;
        }
        $start_submit=Submit::query()->where('id',$start)->first();
        $end_submit=Submit::query()->where('id',$submit)->first();
        if ($start_submit)
        {
            $start_location=Address::query()->where('id',$start_submit->address_id)->first();
            if ($start_location)
            {
                $s=$start_location->lat.','.$start_location->lon;
            }
        }
        if ($end_submit)
        {
            $end_location=Address::query()->where('id',$end_submit->address_id)->first();
            if ($end_location)
            {
                $e=$end_location->lat.','.$end_location->lon;
            }
        }
        if (isset($s) && isset($e))
        {
            try {
                $avg=neshan_route($this->driver->id,$s,$e);
            }catch (\Exception $exception){
                $this->bale->distanceLog('خطا نشان',['start'=>@$start,'end'=>@$submit]);
                $avg=0;
            }
        }
        else{
            $this->bale->distanceLog('عدم تنظیم شروع و پایان',['start'=>@$start,'end'=>@$submit]);
            $avg=0;
        }
//        $avg=neshan_route($this->driver->id,$s,$e);
//        $response = Http::get('https://router.project-osrm.org/route/v1/driving/'.$s.';'.$e.'?overview=false');
//        $route = $response->json();
////        $response = Http::get('https://graphhopper.com/api/1/route?point='.$s.',&point='.$e.'&vehicle=car&key=ba38c972-229a-4e03-b1eb-ed2a9b483bdf');
////        $route = $response->json();
//        $log = fopen("/home/laravel/la.bazistco.com/storage/logs/HopperRouteLog.txt", "a+") or die("Unable to open file!");
//        $t = 'res|'.json_encode($route)."\n";
//        fwrite($log, $t);
////        if (isset($route['paths']))
////        {
////            $avg =$route['paths'][0]['distance']??0;
////        }
////        else{
////            $avg = 0;
////        }
//        if (isset($route['routes'][0]['distance'])) {
//            $avg = $route['routes'][0]['distance']; // Convert meters to kilometers
//        } else {
//            $avg = 0;
//        }
        Cache::set('driver_distance'.$this->driver->id,$avg,3600*24);
        return $avg;
    }
    private function getDistanceV2()
    {
        $sum=0;
        $drivers=$this->drivers;
        if ($drivers->count() > 0)
        {
            for ($i = 0; $i < $drivers->count()-1; $i++) {
                $sum+=$this->getSubmitsDistance($drivers[$i]->submit->address->lat,$drivers[$i]->submit->address->lon,$drivers[$i+1]->submit->address->lat,$drivers[$i+1]->submit->address->lon);
            }
        }
        Cache::set('driver_distance'.$this->driver->id,$sum,3600*24);
        return $sum;

    }
    function getSubmitsDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo) {
        $earthRadius = 6371;
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $earthRadius * $angle;
    }
    private function getTotalAttendance()
    {
        $sum=0;
        $times=DriversAttendanceLogs::query()->where('user_id',$this->driver->id)->whereDate('start_at',now())->get();
        if ($times->count() > 0)
        {
            foreach ($times as $time)
            {
                if ($time->end_at != null)
                {
                    $sum+=$time->time_lenght;
                }
                else{
                    $startAt = Carbon::parse($time->start_at);
                    $sixPM = Carbon::today()->setHour(18)->setMinute(0);
                    $sum+=$startAt->diffInMinutes($sixPM);
                }
            }

            return $sum;
        }
        return 0;
    }
    public function getReward($weight,$distance,$static=false)
    {
        $const=3500;
        return ($const*($weight*0.5))+(4*$const)+$this->getDistanceReward($static,$distance,$const);
    }
    public function getDistanceReward($static,$distance,$const)
    {
        $const=3500;
        if ($static)
        {
            return 14000+120000;
        }
        else{
            return ($const*(2*($distance/1000)));
        }

    }
    private function getWeights($submit)
    {
        $driver=Driver::query()->where('submit_id',$submit)->first();
        if ($driver)
        {
            $recieves=Receive::query()->select(DB::raw('SUM(weight) as weight'))->where('driver_id',$driver->id)->first()->weight??0;
        }
        else{
            $recieves = 0;
        }
        return $recieves;
    }
    private function send_hopper($start,$end,$key,$count=0)
    {
        $response = Http::get('https://graphhopper.com/api/1/route?point='.$start.',&point='.$end.'&vehicle=car&key='.$key);
        $route = $response->json();
        if (isset($route['paths']))
        {
            return $route['paths'][0]['distance']??0;
        }
        else{
            if ($count < 1)
            {
                return $this->send_hopper($start,$end,'8425051f-6566-4f4c-af19-c96de9210f42',1);
            }
            else{
                return 0;
            }
        }
    }
}
