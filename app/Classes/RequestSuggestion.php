<?php

namespace App\Classes;

use App\Models\Car;
use App\Models\Driver;
use App\Models\DriverSuggestedRequests;
use App\Models\Location;
use App\Models\Submit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RequestSuggestion
{
    private mixed $driver_id;
    private DriverSuggestedRequests $suggest_model;
    private $user;
    public $ids = [];

    const CANCEL_STATUS=3;
    const DONE_STATUS=2;
    const RECIEVE_STATUS=1;
    const PENDING_STATUS=0;

    public function __construct($driver_id)
    {
        $this->driver_id=$driver_id;
        $this->user=User::query()->where('id',$driver_id)->first();
        $this->suggest_model=new DriverSuggestedRequests();
    }
    public function cancelDriversSubmit($submit_id)
    {
        DriverSuggestedRequests::query()->where('submit_id',$submit_id)->update(['status'=>3]);
    }
    public function current_suggested()
    {
        $current_suggested=$this->suggest_model::query()->where('driver_id',$this->driver_id)->whereDate('created_at',now())->whereIn('status',[0,1])->get();
        if ($current_suggested->count() > 0)
        {
            return $current_suggested->pluck('submit_id')->toArray();
        }
        else{
            return [];
        }
    }
    public function current_suggested_v2()
    {
        $current_suggested=$this->suggest_model::query()->where('driver_id',$this->driver_id)->whereDate('created_at',now())->whereIn('status',[0,1])->get();
        if ($current_suggested->count() > 0)
        {
            return $current_suggested->pluck('submit_id')->toArray();
        }
        else{
            return [];
        }
    }
    public function sync_requests($ids)
    {
        //حذف درخواست هایی از راننده که در لیست دیگر کاربران در وضعیتی در غیر از انتظار قرار دارند
        $drivers = Driver::query()->whereIn('submit_id',$ids)->whereNot('status',self::PENDING_STATUS)->where('user_id','!=',$this->driver_id)->get();
        if (count($drivers)> 0)
        {
            $delete_ids=$drivers->pluck('submit_id')->toArray();
            DriverSuggestedRequests::query()->whereIn('submit_id',$delete_ids)->where('driver_id',$this->driver_id)->delete();
            $ids=array_diff($ids,$delete_ids);
        }
        return $ids;
    }
    public function get_current_range($hour=null)
    {
        if (!isset($hour))
        {
            $hour=now()->hour;
        }
        $currentHour = $hour+3;
        $results = DB::table('hours')
            ->select('start_at')
            ->whereRaw('? BETWEEN start_at AND end_at', [$currentHour])
            ->first();
        if ($results){
            return $results->start_at;
        }else{

            if (now()->hour < 9)
            {
                return 9;
            }else{
                return 17;
            }
        }

    }
    public function driver_suggesteds()
    {
        //دریافت درخواست های پیشنهادی امروز که در وضعیت در انتظار یا قبول توسط راننده قرار دارند
        $current_suggested=$this->current_suggested();
        $current_suggested=$this->sync_requests($current_suggested);
        //گرفتن شناسه خودرو راننده برای دستیابی به اخرین موقعیت
        $car = Car::query()->where('user_id',$this->user->id)->first();
        //گرفتن لیست مناطق راننده
        $polygons_ids=$this->user->polygonDrivers->pluck('polygon_id');
        if (count($current_suggested) == 1)
        {
            //درصورتی که یک پیشنهاد فعال داشته باشد بر اساس اخرین پیشنهادی دوباره موقعیت نزدیک پیشهاد میشود
            $suggested = $this->latest_suggested();
            if ($suggested)
            {
                $submit_id=$suggested->submit_id;
                $submit=Submit::where('id',$submit_id)
                    ->with(['address' => function ($query) {
                        $query->withTrashed();
                    }])->first();
                $nearest_requests=$this->driverNearRequest($submit->address->lat,$submit->address->lon,$polygons_ids,0,0,$this->today_suggested(),$submit_id);
                //درخواست جدید به پیشنهادی ها اضافه می شود
                $nearest_requests=array_merge($current_suggested,$nearest_requests);
            }
            else{
                $car=Location::query()->where('car_id',$car->id)->latest()->first();
                $lat=$car->lat;
                $lan=$car->long;
                $nearest_requests=$this->driverNearRequest($lat,$lan,$polygons_ids,0,0,$this->today_suggested(),null);
                $nearest_requests=array_merge($current_suggested,$nearest_requests);
            }
        }
        elseif (count($current_suggested) > 0)
        {
            //بدون نیاز به پیشنهاد مجدد
            $nearest_requests=$current_suggested;
        }
        else{
            //درصورتی که هیچ پیشنهادی نباشد اخرین درخواست پیشنهادی به عنوان نقطه شروع پیشنهاد است
            $suggested=$this->latest_suggested();
            $parent_id=null;
            if ($suggested)
            {
                $submit_id=$suggested->submit_id;
                $parent_id=$submit_id;
                $submit=Submit::where('id',$submit_id)
                    ->with(['address' => function ($query) {
                        $query->withTrashed();
                    }])->first();
                $lat=$submit->address->lat;
                $lan=$submit->address->lon;
            }
            else{
                $car=Location::query()->where('car_id',$car->id)->latest()->first();
                $lat=$car->lat;
                $lan=$car->long;
            }
            $nearest_requests=$this->driverNearRequest($lat,$lan,$polygons_ids,parent_id: $parent_id);
        }
        return $nearest_requests;
    }

    public function driver_suggesteds_v2()
    {
        $current_suggested=$this->current_suggested_v2();
        $polygons_ids=$this->user->polygonDrivers->pluck('polygon_id');
        $driver_id = $this->driver_id;
        $car = Car::query()->where('user_id',$this->user->id)->first();
        $location = Location::query()->where('car_id',$car->id)->latest()->first();
        $parent_id=$this->findParent();
        if (count($current_suggested) > 0)
        {
            dd('sgggg');
        }
        else{
            $first_nearest=$this->findNearestV2($location->lat,$location->long,$polygons_ids,$current_suggested);
            if ($first_nearest['status'] == true)
            {
                foreach ($first_nearest['ids'] as $nearest)
                {
                    $this->ids[]=$nearest;
                    $start_at=Submit::query()->where('id',$nearest)->first()->start_deadline;
                    \App\Models\DriverSuggestedRequests::query()->create(['driver_id'=>$driver_id,'submit_id'=>$nearest,'parent_id'=>$parent_id,'start_at'=>$start_at]);
                    $parent_id = $nearest;
                }
            }
        }
        $current_suggested=$this->current_suggested_v2();
        $today_request=$this->today_requests($polygons_ids);
        $new_submits=array_diff($today_request,$current_suggested);
        if (count($new_submits)>0)
        {
            DriverSuggestedRequests::query()->whereIn('submit_id',$current_suggested)->where('driver_id',$driver_id)->delete();
            $current_suggested=$this->current_suggested_v2();
            $first_nearest=$this->findNearestV2($location->lat,$location->long,$polygons_ids,$current_suggested);
            if ($first_nearest['status'] == true)
            {
                foreach ($first_nearest['ids'] as $nearest)
                {
                    $this->ids[]=$nearest;
                    $start_at=Submit::query()->where('id',$nearest)->first()->start_deadline;
                    \App\Models\DriverSuggestedRequests::query()->create(['driver_id'=>$driver_id,'submit_id'=>$nearest,'parent_id'=>$parent_id,'start_at'=>$start_at]);
                    $parent_id = $nearest;
                }
            }
        }


        dd('here');

        $response = Http::withHeaders([
            'Api-Key' => 'service.yoZD3QCLQAPUweIxRrKWV0eXCx69JGTfIqPpCEEy'
        ])->get("https://api.neshan.org/v3/trip?waypoints=&roundTrip=false&sourceIsAnyPoint=false");

    }

    private function latest_suggested()
    {
//        $hour=$this->get_current_range();
        $current_hour=$this->get_current_range();
        if ($current_hour == 17)
        {
            $current_hour = $current_hour+4;
        }
        //دوساعت اضافه شد چون در بازه فعلی امکان ثبت فوری تا دو ساعت اینده در این بازه وجود دارد
        return $this->suggest_model::query()->where('driver_id',$this->user->id)->whereNotIn('status',[3])->whereDate('created_at',now()) ->where(function ($query) use ($current_hour) {
            $query->whereRaw('HOUR(start_at) <= ?', [$current_hour])
                ->orWhere('is_emergency', 1);
              })->orderByDesc('id')->first();

//        return $this->suggest_model::query()->where('driver_id',$this->user->id)->whereNotIn('status',[3])->whereDate('created_at',now())->whereRaw('HOUR(start_at) between  ? and ?', [$hour,now()->hour])->orderByDesc('id')->first();
    }
    private function latest_done()
    {
        return $this->suggest_model::query()->where('driver_id',$this->user->id)->where('status',2)->whereDate('created_at',now())->orderByDesc('id')->first();
    }
    private function today_suggested()
    {
        return $this->suggest_model::query()->where('driver_id',$this->user->id)->whereDate('created_at',now())->orderByDesc('id')->get()->pluck('submit_id')->toArray();

    }
    private function driverNearRequest($driverLatitude,$driverLongitude,$polygons,$repeat_count = 1,$start=0,$except_ids=[],$parent_id = null)
    {
        $count=$start;
        $user = $this->user;
        $driver_id=$user->id;
        $first_nearest =$this->findNearest($driverLatitude,$driverLongitude,$polygons,$except_ids);
        if ($first_nearest['status'])
        {
            $this->ids[]=$first_nearest['submit_id'];
            $start_at=Submit::query()->where('id',$first_nearest['submit_id'])->first()->start_deadline;
            \App\Models\DriverSuggestedRequests::query()->create(['driver_id'=>$driver_id,'submit_id'=>$first_nearest['submit_id'],'parent_id'=>$parent_id,'start_at'=>$start_at]);
            if ($count < $repeat_count)
            {
                $count+=1;
                $submit=Submit::where('id',$first_nearest['submit_id'])->with(['address' => function ($query) {
                        $query->withTrashed();
                    }])->first();
                $except_ids[]=$first_nearest['submit_id'];
                $this->driverNearRequest($submit->address->lat,$submit->address->lon,$polygons,$repeat_count,$count,$except_ids,$first_nearest['submit_id']);
            }
            return $this->ids;
        }
        else{
            return [];
        }

    }
    public function deleteExist($submit_id)
    {
        DriverSuggestedRequests::query()->where('submit_id',$submit_id)->where('driver_id',$this->driver_id)->delete();
    }
    public function deleteDriversMove($submit_id)
    {
        DriverSuggestedRequests::query()->where('submit_id',$submit_id)->delete();
    }
    public function regenerate_suggestion($submit_id)
    {
//        $new_seggesteds=[];
       $this->deleteExist($submit_id);
       $ids=$this->current_suggested();
       $this->sync_requests($ids);
       $suggested=$this->latest_suggested();
        if ($suggested)
        {
            $parent_id=$suggested->submit_id;
        }
        else{
            $parent_id=null;
        }
        DriverSuggestedRequests::query()->insert(['is_emergency'=>1,'parent_id'=>$parent_id,'start_at'=>@Submit::query()->where('id',$submit_id)->first()->start_deadline,'submit_id'=>$submit_id,'driver_id'=>$this->user->id,'status'=>1,'updated_at'=>now()->format('Y-m-d'),'created_at'=>now()->format('Y-m-d')]);
        return true;

    }
    public function first_submit($ids)
    {
        $currentTime=now();
        $nearest= \Illuminate\Support\Facades\DB::table('submits as s')
            ->join('addresses as ad', 's.address_id', '=', 'ad.id')
            ->select(
                'ad.lat',
                'ad.lon',
                's.id',
                's.start_deadline',
                's.end_deadline',
                's.is_instant',
                \Illuminate\Support\Facades\DB::raw("HOUR(s.start_deadline) as h"),
                \Illuminate\Support\Facades\DB::raw("TIMESTAMPDIFF(SECOND, '$currentTime', s.end_deadline) AS time_to_end")
            )
            ->whereDate('start_deadline', $currentTime)
            ->whereIn('s.id', $ids)
            ->orderBy('time_to_end');
       return $nearest->first();
    }
    private function get_time_range($query)
    {
        $current_hour=$this->get_current_range();
        if ($current_hour == 17)
        {
            $current_hour = $current_hour+4;
        }
        //دو ساعت به بازه فعلی اضافه میکنیم تا فوری های در اون بازه را هم در نظر بگیرد
        return $query->whereRaw('HOUR(s.start_deadline) <= ?', [$current_hour]);
    }
    private function findNearest($driverLatitude,$driverLongitude,$polygons,$submit_id=[])
    {
        $currentTime=now();
        $nearest= \Illuminate\Support\Facades\DB::table('submits as s')
            ->join('addresses as ad', 's.address_id', '=', 'ad.id')
            ->select(
                'ad.lat',
                'ad.lon',
                's.id',
                's.start_deadline',
                's.end_deadline',
                's.is_instant',
                \Illuminate\Support\Facades\DB::raw("HOUR(s.start_deadline) as h"),
                \Illuminate\Support\Facades\DB::raw("
          (6371 * acos(
                cos(radians($driverLatitude))
                * cos(radians(ad.lat))
                * cos(radians(ad.lon) - radians($driverLongitude))
                + sin(radians($driverLatitude))
                * sin(radians(ad.lat))
            )) AS distance
        "), // محاسبه فاصله از راننده
                \Illuminate\Support\Facades\DB::raw("TIMESTAMPDIFF(SECOND, '$currentTime', DATE_FORMAT(s.end_deadline, '%Y-%m-%d %H:00:00')) AS time_to_end")
            )
            ->whereDate('start_deadline', $currentTime) // فیلتر تاریخ
            ->whereIn('s.region_id', $polygons) // فیلتر مناطق
            ->where('s.status', 1) // فقط نقاط فعال
            ->orderBy('time_to_end')
            ->orderBy('distance') // مرتب‌سازی بر اساس فاصله
            ->orderByDesc('s.is_instant') // اولویت درخواست فوری
            ->orderBy('h');
        if (count($submit_id)>0)
        {
            $nearest=$nearest->whereNotIn('s.id',$submit_id);
        }
        //درخواست ها پیشنهادی به این صورت است که تا بازه دوساعت آینده ثبت شده باشند تا درخواست های خارج از بازه و برای بازه های آتی پیشنهاد نشود
        $nearest = $this->get_time_range($nearest);
        $nearest = $nearest->get();
        if (count($nearest) == 0) {
            return ['status'=>false,'submit_id'=>null];
        }
        if (count($nearest) == 1) {
            return ['status'=>true,'submit_id'=>$nearest[0]->id];
        }
        $nearest_hour = $nearest[0]->h;
        if ($nearest_hour == 17)
        {
            $nearest_hour = $nearest_hour+4;
        }
        $dis = [];
        $dis['from'] = [$driverLatitude . ',' . $driverLongitude];
        $submits = [];
        foreach ($nearest as $nearest_item) {
            if ($nearest_item->h <= $nearest_hour) {
                $submits[] = $nearest_item->id;
                $dis['to'][] = $nearest_item->lat . ',' . $nearest_item->lon;
            }
        }
        if (count($submits) > 1)
        {
            $result=neshanGetDistance($dis);
            if (!$result['status'])
            {
                return ['status'=>false,'submit_id'=>null];
            }
            return ['status'=>true,'submit_id'=>$submits[$result['index']]];

        }
        else{
            return ['status'=>false,'submit_id'=>null];
        }
    }
    private function findNearestV2($driverLatitude,$driverLongitude,$polygons,$submit_id=[])
    {
        $response=['status'=>false,'ids'=>null];
        $currentTime=now();
        $nearest= \Illuminate\Support\Facades\DB::table('submits as s')
            ->join('addresses as ad', 's.address_id', '=', 'ad.id')
            ->select(
                'ad.lat',
                'ad.lon',
                's.id',
                's.start_deadline',
                's.end_deadline',
                's.is_instant',
                \Illuminate\Support\Facades\DB::raw("HOUR(s.start_deadline) as h"),
                \Illuminate\Support\Facades\DB::raw("TIMESTAMPDIFF(SECOND, '$currentTime', DATE_FORMAT(s.end_deadline, '%Y-%m-%d %H:00:00')) AS time_to_end")
            )
            ->whereDate('start_deadline', $currentTime) // فیلتر تاریخ
            ->whereIn('s.region_id', $polygons) // فیلتر مناطق
            ->where('s.status', 1) // فقط نقاط فعال
            ->orderBy('time_to_end');
        if (count($submit_id)>0)
        {
            $nearest=$nearest->whereNotIn('s.id',$submit_id);
        }
        //درخواست ها پیشنهادی به این صورت است که تا بازه دوساعت آینده ثبت شده باشند تا درخواست های خارج از بازه و برای بازه های آتی پیشنهاد نشود
        $nearest = $this->get_time_range($nearest);
        $nearest = $nearest->get();
        if (count($nearest) == 0) {
            return ['status'=>false,'ids'=>null];
        }
        if (count($nearest) == 1) {
            return ['status'=>true,'ids'=>[$nearest[0]->id]];
        }
        $dis = [];
        $dis[] = $driverLatitude . ',' . $driverLongitude;
        $submits = [];
        $submits[]=null;
        foreach ($nearest as $nearest_item) {
                $submits[] = $nearest_item->id;
                $dis[] = $nearest_item->lat . ',' . $nearest_item->lon;
        }
        dd($submits);
        if (count($submits) > 1)
        {
            $params=implode('|',$dis);
            $response = Http::withHeaders([
                'Api-Key' => 'service.yoZD3QCLQAPUweIxRrKWV0eXCx69JGTfIqPpCEEy'
            ])->get("https://api.neshan.org/v3/trip?waypoints={$params}&roundTrip=false&sourceIsAnyPoint=false");
            $result=$response->json();
            $points=$result['points'];
            $indexes = array_column($points, 'index');
//            $indexes= [0,1,4,2,3];
            unset($submits[0]);
            unset($indexes[0]);
            $optimize_submits=[];
            foreach ($indexes as $index) {
                $optimize_submits[]=$submits[$index];
            }
            return ['status'=>true,'ids'=>$optimize_submits];
            return $optimize_submits;
            $result=neshanGetDistance($dis);
            if (!$result['status'])
            {
                return ['status'=>false,'submit_id'=>null];
            }
            return ['status'=>true,'submit_id'=>$submits[$result['index']]];

        }
        else{
            return ['status'=>false,'submit_id'=>null];
        }
    }
    private function today_requests($polygons){
         $currentTime=now();
        $nearest= \Illuminate\Support\Facades\DB::table('submits as s')
            ->join('addresses as ad', 's.address_id', '=', 'ad.id')
            ->select('s.id')
            ->whereDate('start_deadline', $currentTime)
            ->whereIn('s.region_id', $polygons)
            ->where('s.status', 1) ;
            $nearest = $this->get_time_range($nearest);
            return $nearest->get()->pluck('id')->toArray();
       }

    private function findParent()
    {
        return null;
    }

}

