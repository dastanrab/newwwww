<?php

namespace App\Classes;

use App\Models\Address;
use App\Models\Car;
use App\Models\Driver;
use App\Models\DriverSuggestedRequests;
use App\Models\Location;
use App\Models\NeshanApiLog;
use App\Models\PolygonDriver;
use App\Models\Submit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RequestSuggestionV2
{
    private mixed $driver_id;
    private $is_driver_location = 0;
    private DriverSuggestedRequests $suggest_model;
    private $user;
    public $ids = [];

    const CANCEL_STATUS=3;
    const DONE_STATUS=2;
    const RECIEVE_STATUS=1;
    const PENDING_STATUS=0;
    public $new_move_submit;
    public  $in_reigions;

    public function __construct($driver_id)
    {
        $this->driver_id=$driver_id;
        $this->user=User::query()->where('id',$driver_id)->first();
        $this->suggest_model=new DriverSuggestedRequests();
        $this->polygons_ids=PolygonDriver::query()->where('user_id',$this->driver_id)->pluck('polygon_id')->toArray();
    }
    public function cancelDriversSubmit($submit_id)
    {
        DriverSuggestedRequests::query()->where('submit_id',$submit_id)->update(['status'=>3]);
    }
    public function current_suggested_v2()
    {
        //todo add out_region where
        $current_suggested=$this->suggest_model::query()->where('driver_id',$this->driver_id)->whereDate('start_at',now())->whereIn('status',[0,1])->orderBy('id')->get();
        if ($current_suggested->count() > 0)
        {
            return $current_suggested->pluck('submit_id')->toArray();
        }
        else{
            return [];
        }
    }
    public function allowed_current_suggested_v2()
    {
        $current_suggested=$this->suggest_model::query()->where('driver_id',$this->driver_id)->whereDate('start_at',now())->where('start_at','>=',now()->subHours(2))->Where('in_regions', 1)->whereIn('status',[0,1])->orderBy('id')->get();
        if ($current_suggested->count() > 0)
        {
            return $current_suggested->pluck('submit_id')->toArray();
        }
        else{
            return [];
        }
    }
    public function current_pending_suggested()
    {
        //todo add out_region where
        $current_suggested=$this->suggest_model::query()->where('driver_id',$this->driver_id)->whereDate('start_at',now())->whereIn('status',[0])->get();
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
        $ids=$this->remove_reciveve_submits($ids);
        $current_requests=$this->current_pending_suggested();
        //حذف در خواست ها پیشنهادی در وضعیت انتظار که در مناطق فعال راننده نیست
        return count($current_requests)>0?$this->remove_out_region_submits($ids,$current_requests):$ids;

    }
    public function remove_out_region_submits($ids,$pending_submits)
    {
            $out_of_regions=Submit::query()->select(['id'])->whereIn('id',$pending_submits)->whereNotIn('region_id',$this->polygons_ids)->get();
            if (count($out_of_regions)>0)
            {
                $delete_ids=$out_of_regions->pluck('id')->toArray();
                DriverSuggestedRequests::query()->whereIn('submit_id',$delete_ids)->where('driver_id',$this->driver_id)->delete();
                $ids=array_diff($ids,$delete_ids);
            }
            return $ids;
    }
    public function remove_reciveve_submits($ids)
    {
        $drivers = Driver::query()->whereIn('submit_id',$ids)->whereNot('status',self::PENDING_STATUS)->where('user_id','!=',$this->driver_id)->get();
        if (count($drivers)> 0)
        {
            $delete_ids=$drivers->pluck('submit_id')->toArray();
            DriverSuggestedRequests::query()->whereIn('submit_id',$delete_ids)->where('driver_id',$this->driver_id)->delete();
            return array_diff($ids,$delete_ids);
        }
        return $ids;
    }
    public function get_current_range($hour=null)
    {
        if (!isset($hour))
        {
            $hour=now()->hour;
        }
        $currentHour = $hour+2;
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
    public function get_next_range($hour=null)
    {
        if (!isset($hour))
        {
            $hour=now()->hour;
        }
        //اختلاف بازه های زمانی دو ساعت است و با این کار بازه بعد را پیدا میکند
        $currentHour = $hour+2;
        $results = DB::table('hours')
            ->select('start_at')
            ->whereRaw('? BETWEEN start_at AND end_at', [$currentHour])
            ->first();
        if ($results){
            return $results->start_at+2;
        }else{

            if (now()->hour < 9)
            {
                return 11;
            }else{
                return 17;
            }
        }
    }
    private function delete_current_suggesteds($current_suggesteds)
    {
        DriverSuggestedRequests::query()->whereIn('submit_id',$current_suggesteds)->where('driver_id',$this->driver_id)->whereDate('start_at',now())->delete();
    }
    private function current_recieve_requests()
    {
        //todo add out_region where
        $current_suggested=$this->suggest_model::query()->where('driver_id',$this->driver_id)->whereDate('start_at',now())->where('start_at','>=',now()->subHours(2))->Where('in_regions', 1)->whereIn('status',[1])->get();
        if ($current_suggested->count() > 0)
        {
            return $current_suggested->pluck('submit_id')->toArray();
        }
        else{
            return [];
        }
    }
    private function reg_current_recieve_requests()
    {
        //todo add out_region where
        $current_suggested=$this->suggest_model::query()->where('driver_id',$this->driver_id)->whereDate('start_at',now())->where('start_at','>=',now()->subHours(2))->Where('in_regions', 1)->whereIn('status',[0,1])->get();
        if ($current_suggested->count() > 0)
        {
            return $current_suggested->pluck('submit_id')->toArray();
        }
        else{
            return [];
        }
    }
    public function driver_suggesteds_v2()
    {
        //دریافت درخواست های پیشنهادی جاری کاربر(وضعیت در انتظار و برداشته شده)
        $current_suggested=$this->current_suggested_v2();
        //بروزکردن درخواست ها پیشنهادی جاری کاربر
        $this->sync_requests($current_suggested);
        $current_suggested=$this->current_suggested_v2();
        $allowed_current_suggested=$this->allowed_current_suggested_v2();
        //بررسی وجود درخواست جدید در بازه زمانی فعلی
        $new_submits=$this->exist_new_submits();
        if (count($new_submits) > 0)
        {
            //دیافت پدر برای موقعیت یابی جدید
            $parent=$this->NewSubmitParent();
            //دریافت لیست درخواست های انجام شده راننده
            $done_submits=$this->today_dones();
            $un_recieve_requests=$this->un_recieve_requests();
            $done_submits=array_merge($un_recieve_requests,$done_submits);
            //دریافت لیست درخواست های پیشنهادی دریافت شده جاری راننده
            $current_recieve_requests=$this->current_recieve_requests();
            //دریافت درخواست ها برای مسیر یابی
            $requests=$this->getRequests($this->polygons_ids,$done_submits,$current_recieve_requests);
            if ($requests['status'])
            {
//                $current_pendings=$this->current_pending_suggested();
                $past_suggesteds=$this->get_past_suggesteds($allowed_current_suggested);
                $best_path=$this->get_optimize_route($parent['lat'],$parent['lon'],$requests);
                try {
                    DB::beginTransaction();
                    $this->delete_current_suggesteds($allowed_current_suggested);
                    $this->store_suggests($best_path,$parent,$past_suggesteds);
                    DB::commit();
                }catch (\Exception $e){
                            DB::rollBack();
                }

            }
            return $this->current_suggested_v2();
        }
        if (count($current_suggested) >= 3)
        {
            return $current_suggested;
        }
        else{
            $current_suggested=$this->current_suggested_v2();
            $parent=$this->findParent();
            $requests=$this->getRequests($this->polygons_ids,$current_suggested);
            if ($requests['status'])
            {
                $best_path=$this->get_optimize_route($parent['lat'],$parent['lon'],$requests);
                if ($best_path['status'])
                {
                    $this->store_suggests($best_path,$parent);
                }

            }
        }
        return $this->current_suggested_v2();
    }
    private function today_dones()
    {
        $done_submits=DriverSuggestedRequests::query()->whereIn('status',[2,3])->where('driver_id',$this->driver_id)->whereDate('start_at',now())->get();
        if (count($done_submits) > 0)
        {
            return $done_submits->pluck('submit_id')->toArray();
        }
        return [];
    }

    private function latest_suggested()
    {
        $current_hour=$this->get_current_range();
        if ($current_hour == 17)
        {
            $current_hour = $current_hour+4;
        }
        //دوساعت اضافه شد چون در بازه فعلی امکان ثبت فوری تا دو ساعت اینده در این بازه وجود دارد
        return $this->suggest_model::query()
            ->where('driver_id',$this->user->id)
            ->whereNotIn('status',[3])
            ->whereDate('start_at',now())
            ->where(function ($query) use ($current_hour) {
            $query->whereRaw('HOUR(start_at) <= ?', [$current_hour])
                ->orWhere('is_emergency', 1)
                ->orWhereIn('status',[1,2]);
              })
            ->orderByDesc('id')->first();
    }
    private function exist_new_submits()
    {
        //دریافت درخواست های جاری
          $current_suggested=$this->current_suggested_v2();
          //دریافت درخواست های بازه فعلی در مناطق راننده
          $today_request=$this->today_requests($this->polygons_ids);
          //دریافت درخواست های جدید
          return array_diff($today_request,$current_suggested);
    }
    private function latest_done()
    {
        $latest=$this->suggest_model::query()->where('driver_id',$this->driver_id)->whereDate('start_at',now())
            ->where('start_at', '<=', now()->subHours(2))
            //کامنت شد چون باید با خارج ار منطقه بی اثر رفتار کرد
//            ->where(function ($query) {
//            $query->where('start_at', '<=', now()->subHours(2))
//                ->orWhere('in_regions', 0);
//        })
//            ->whereIn('status',[1,2])->orderByDesc('id')->first();
            ->whereIn('status',[0,1,2])->orderByDesc('id')->first();

        if ($latest)
        {
            return $latest;
        }
        return $this->suggest_model::query()->where('driver_id',$this->user->id)->whereIn('status',[2])->whereDate('start_at',now())->orderByDesc('id')->first();
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
    private function get_time_range($query)
    {
        $current_hour=$this->get_current_range();
        if ($current_hour == 17)
        {
            $current_hour = $current_hour+4;
        }
        return $query->whereRaw('HOUR(s.start_deadline) <= ?', [$current_hour+1]);
    }
    private function get_time_next_range($query)
    {
        $current_hour=$this->get_next_range();
        if ($current_hour == 17)
        {
            $current_hour = $current_hour+4;
        }
        //دو ساعت به بازه فعلی اضافه میکنیم تا فوری های در اون بازه را هم در نظر بگیرد
        return $query->whereRaw('HOUR(s.start_deadline) <= ?', [$current_hour]);
    }
    private function getRequests($polygons,$submit_id=[],$past_requests=[])
    {
        $currentTime=now();
        //دریافت نزدیکترین درخواست ها
        $nearest= $this->nearests($submit_id);
        $range=$this->get_current_range();
        $current_range_pending=DriverSuggestedRequests::query()->where('driver_id',$this->user->id)->whereDate('start_at',now())->where(function ($query) use ($range) {
            $query->whereRaw('HOUR(start_at) <= ?', [$range+1]);
        })->where('status',0)->get();
        //اگر درخواست نزدیکی نباشد و درخواستی هم از بازه زمانی جاری با وضعیت در انتظار ,در پیشنهاد ها نباشد از بازه های بعدی پیشنهاد میدهد
        if (count($nearest) == 0 and count($current_range_pending) == 0) {
            $nearest=$this->next_range_nearests($submit_id);
            if (count($nearest) == 0) {
                return ['status'=>false,'ids'=>[]];
            }

        }
        $ids=$nearest->toArray();
        if (count($past_requests) > 0)
        {
            $old_suggesteds=\Illuminate\Support\Facades\DB::table('submits as s')
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
                )->whereIn('s.id',$past_requests)->get()->toArray();
            $ids=array_merge($ids,$old_suggesteds);
        }
        return ['status'=>true,'ids'=>$ids];

    }
    private function get_optimize_route($start_lat,$start_lan,$requests)
    {
        $dis = [];
        $dis[] = $start_lat . ',' . $start_lan;
        $start_info=$start_lat . ',' . $start_lan;
        $submits = [];
        $submits[]=null;
        foreach ($requests['ids'] as $request) {
            $submits[] = $request->id;
            $dis[] = $request->lat . ',' . $request->lon;
        }
        if (count($submits) > 1)
        {
            $params=implode('|',$dis);
            $response = Http::withHeaders([
                'Api-Key' => 'service.yoZD3QCLQAPUweIxRrKWV0eXCx69JGTfIqPpCEEy'
            ])->get("https://api.neshan.org/v3/trip?waypoints={$params}&roundTrip=false&sourceIsAnyPoint=false");
            if ($response->ok())
            {
                NeshanApiLog::query()->create(['is_driver_location'=>$this->is_driver_location,'user_id'=>$this->user->id,'start_info'=>$start_info,'endpoint'=>'/v3/trip','request_data'=>"waypoints={$params}&roundTrip=false&sourceIsAnyPoint=false",'response_data'=>[$response->json(),$submits],'status_code'=>$response->status()]);
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
            }
            else{
                NeshanApiLog::query()->create(['is_driver_location'=>$this->is_driver_location,'user_id'=>$this->user->id,'start_info'=>$start_info,'endpoint'=>'/v3/trip','request_data'=>"wapoints={$params}&roundTrip=false&sourceIsAnyPoint=false",'response_data'=>$response->json(),'status_code'=>$response->status()]);
                abort(response()->json([
                    'status'  => 'error',
                    'message' => 'خطا در ارتباط با نشان',
                    'data'    => null,
                ], 200)->header('Content-type', 'application/json'), 200);
            }
        }
        else{
            return ['status'=>false,'submit_id'=>null];
        }
    }
    private function today_requests($polygons){
         $currentTime=now();
        $nearest= \Illuminate\Support\Facades\DB::table('submits as s')
            ->select('s.id')
            ->whereDate('start_deadline', $currentTime)
            ->whereIn('s.region_id', $polygons)
            ->where('s.status', 1) ;
            $nearest = $this->get_time_range($nearest);
            return $nearest->get()->pluck('id')->toArray();
       }
    private function NewSubmitParent()
    {
        //دریافت اخرین شناسه اخرین درخواست انجام شده راننده
        $parent_id=$this->latest_done();
        if ($parent_id)
        {
            $this->is_driver_location=$parent_id->submit_id;
            $submit=Submit::query()->where('id',$parent_id->submit_id)->select('address_id')->first();
            $address=Address::query()->where('id',$submit->address_id)->first();
            return ['parent_id'=>$parent_id->submit_id,'lat'=>$address->lat,'lon'=>$address->lon,];
        }
        else{
            //درصورت درخواست انجام نداده شده باشد اخرین موقعیت راننده استفاده می شود
            $this->is_driver_location=1;
            $car = Car::query()->where('user_id',$this->user->id)->first();
            $location = Location::query()->where('car_id',$car->id)->latest()->first();
            return ['parent_id'=>null,'lat'=>$location->lat,'lon'=>$location->long];
        }

    }
    private function findParent()
    {
        $parent_id=$this->latest_suggested();
        if ($parent_id)
        {
            $this->is_driver_location=$parent_id->submit_id;
            $submit=Submit::query()->where('id',$parent_id->submit_id)->select('address_id')->first();
            $address=Address::query()->where('id',$submit->address_id)->first();
            return ['parent_id'=>$parent_id->submit_id,'lat'=>$address->lat,'lon'=>$address->lon,];
        }
        else{
            $this->is_driver_location=1;
            $car = Car::query()->where('user_id',$this->user->id)->first();
            $location = Location::query()->where('car_id',$car->id)->latest()->first();
            return ['parent_id'=>null,'lat'=>$location->lat,'lon'=>$location->long];
        }

    }
    private function store_suggests($routes,$parent,$past_suggested=null)
    {
        $driver_id=$this->driver_id;
        foreach ($routes['ids'] as $request)
        {
            $this->ids[]=$request;
            if (isset($past_suggested))
            {
                $status= $past_suggested[$request]['status'] ?? 0;
                $is_emergency=$past_suggested[$request]['is_emergency'] ?? 0;
                $in_region=$past_suggested[$request]['in_regions'] ?? 1;
            }
            else{
                $status=0;
                $is_emergency=0;
                $in_region=1;
            }
            $start_at=Submit::query()->where('id',$request)->first()->start_deadline;
             $exist=\App\Models\DriverSuggestedRequests::query()->where('driver_id',$this->driver_id)->where('submit_id',$request)->exists();
            if (!$exist)
            {
                try {
                    \App\Models\DriverSuggestedRequests::query()->create(['driver_id'=>$driver_id,'submit_id'=>$request,'parent_id'=>$parent['parent_id'],'start_at'=>$start_at,'status'=>$status,'is_emergency'=>$is_emergency,'in_regions'=>$in_region]);
                }catch (\Exception $e){

                }
                $parent['parent_id'] = $request;

            }

        }
    }
    private function reg_store_suggests($routes,$parent,$past_suggested=null)
    {
        $driver_id=$this->driver_id;
        foreach ($routes['ids'] as $request)
        {
            $this->ids[]=$request;
            if (isset($past_suggested[$request]))
            {
                $status= $past_suggested[$request]['status'] ?? 0;
                $is_emergency=$past_suggested[$request]['is_emergency'] ?? 0;
                $in_region=$past_suggested[$request]['in_regions'] ?? 1;
            }
            else{
                if ($this->new_move_submit == $request)
                {
                    $status=1;
                    $is_emergency=1;
                    $in_region=$this->in_reigions;
                    DriverSuggestedRequests::query()->where('submit_id',$request)->where('driver_id',$this->driver_id)->update(['status'=>1,'is_emergency'=>1,'in_regions'=>1]);

                }
                else{
                    $status=0;
                    $is_emergency=0;
                    $in_region=0;
                }

            }
            $start_at=Submit::query()->where('id',$request)->first()->start_deadline;
            $exist=\App\Models\DriverSuggestedRequests::query()->where('driver_id',$this->driver_id)->where('submit_id',$request)->exists();
            if (!$exist)
            {
                try {
                    \App\Models\DriverSuggestedRequests::query()->create(['driver_id'=>$driver_id,'submit_id'=>$request,'parent_id'=>$parent['parent_id'],'start_at'=>$start_at,'status'=>$status,'is_emergency'=>$is_emergency,'in_regions'=>$in_region]);
                }catch (\Exception $e){

                }
                $parent['parent_id'] = $request;

            }

        }
    }
    private function get_past_suggesteds($current_suggested)
    {
        $past_suggesteds=\App\Models\DriverSuggestedRequests::query()->whereIn('submit_id',$current_suggested)->whereDate('start_at',now())->where('driver_id',$this->driver_id)->get();
        if (count($past_suggesteds)>0)
        {
            return $past_suggesteds->keyBy('submit_id')->toArray();
        }
        return [];
    }
    private function get_submit_details($ids)
    {
        return \Illuminate\Support\Facades\DB::table('submits as s')
            ->join('addresses as ad', 's.address_id', '=', 'ad.id')
            ->select(
                'ad.lat',
                'ad.lon',
                's.id',
                's.start_deadline',
                's.end_deadline',
                's.is_instant',
            )->whereIn('s.id',$ids)->get()->toArray();
    }
    public function regenerate_suggestion($submit_id)
    {
        $this->new_move_submit=$submit_id;
        $region_id=Submit::query()->where('id',$submit_id)->first()->region_id;
        $this->in_reigions= in_array($region_id, $this->polygons_ids) ?1:0;
        if(DriverSuggestedRequests::query()->where('submit_id',$submit_id)->where('driver_id',$this->driver_id)->exists())
        {
            DriverSuggestedRequests::query()->where('submit_id',$submit_id)->whereNot('driver_id',$this->driver_id)->delete();
            DriverSuggestedRequests::query()->where('submit_id',$submit_id)->where('driver_id',$this->driver_id)->update(['status'=>1,'is_emergency'=>1,'in_regions'=>$this->in_reigions]);
            return true;
        }
        //start
        $current_suggested=$this->current_suggested_v2();
        //بروزکردن درخواست ها پیشنهادی جاری کاربر
        $this->sync_requests($current_suggested);
        $current_suggested=$this->current_suggested_v2();
        $allowed_current_suggested=$this->allowed_current_suggested_v2();
        $ids=$this->get_submit_details([$submit_id]);
        //دیافت پدر برای موقعیت یابی جدید
            $parent=$this->NewSubmitParent();
            //دریافت لیست درخواست های انجام شده راننده
            $done_submits=$this->today_dones();
            $un_recieve_requests=$this->un_recieve_requests();
            $done_submits=array_merge($un_recieve_requests,$done_submits);
            //دریافت لیست درخواست های پیشنهادی دریافت شده جاری راننده
            $current_recieve_requests=$this->reg_current_recieve_requests();
            $current_recieve_requests=$this->get_submit_details($current_recieve_requests);
            //دریافت درخواست ها برای مسیر یابی
            $ids=array_merge($ids,$current_recieve_requests);
            $requests=['status'=>true,'ids'=>$ids];
            if ($requests['status'])
            {
                $past_suggesteds=$this->get_past_suggesteds($allowed_current_suggested);
                $best_path=$this->get_optimize_route($parent['lat'],$parent['lon'],$requests);
                try {
                    DB::beginTransaction();
                    $this->delete_current_suggesteds($allowed_current_suggested);
                    $this->reg_store_suggests($best_path,$parent,$past_suggesteds);
                    DB::commit();
                }catch (\Exception $e){
                    DB::rollBack();
                }

            }
        //stop
//        DriverSuggestedRequests::query()->where('submit_id',$submit_id)->delete();
//        $ids=$this->current_suggested_v2();
//        $this->sync_requests($ids);
//        $suggested=$this->latest_suggested();
//        if ($suggested)
//        {
//            $parent_id=$suggested->submit_id;
//        }
//        else{
//            $parent_id=null;
//        }
//        DriverSuggestedRequests::query()->insert(['in_regions'=>$this->in_reigions,'is_emergency'=>1,'parent_id'=>$parent_id,'start_at'=>@Submit::query()->where('id',$submit_id)->first()->start_deadline,'submit_id'=>$submit_id,'driver_id'=>$this->user->id,'status'=>1,'updated_at'=>now()->format('Y-m-d'),'created_at'=>now()->format('Y-m-d')]);
        return true;

    }
    public function nearests($submit_id)
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
                \Illuminate\Support\Facades\DB::raw("TIMESTAMPDIFF(SECOND, '$currentTime', DATE_FORMAT(s.end_deadline, '%Y-%m-%d %H:00:00')) AS time_to_end")
            )
            ->whereDate('start_deadline', $currentTime) // فیلتر تاریخ
            ->whereIn('s.region_id', $this->polygons_ids) // فیلتر مناطق
            ->where('s.status', 1) // فقط نقاط فعال
            ->orderBy('time_to_end');
        if (count($submit_id)>0)
        {
            $nearest=$nearest->whereNotIn('s.id',$submit_id);
        }
        //درخواست ها پیشنهادی به این صورت است که تا بازه یک ساعت آینده ثبت شده باشند تا درخواست های خارج از بازه و برای بازه های آتی پیشنهاد نشود
        $nearest = $this->get_time_range($nearest);
        return $nearest->get();
    }
    public function next_range_nearests($submit_id)
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
                \Illuminate\Support\Facades\DB::raw("TIMESTAMPDIFF(SECOND, '$currentTime', DATE_FORMAT(s.end_deadline, '%Y-%m-%d %H:00:00')) AS time_to_end")
            )
            ->whereDate('start_deadline', $currentTime) // فیلتر تاریخ
            ->whereIn('s.region_id', $this->polygons_ids) // فیلتر مناطق
            ->where('s.status', 1) // فقط نقاط فعال
            ->orderBy('time_to_end');
        if (count($submit_id)>0)
        {
            $nearest=$nearest->whereNotIn('s.id',$submit_id);
        }
        //درخواست ها پیشنهادی به این صورت است که تا بازه دوساعت آینده ثبت شده باشند تا درخواست های خارج از بازه و برای بازه های آتی پیشنهاد نشود
        $nearest = $this->get_time_next_range($nearest);
        return $nearest->get();
    }

    private function un_recieve_requests()
    {
        $current_suggested=$this->suggest_model::query()->where('driver_id',$this->driver_id)->whereDate('start_at',now())->where(function ($query) {
            $query->where('start_at', '<=', now()->subHours(2))
                ->orWhere('in_regions', 0);
//        })->whereIn('status',[1])->orderBy('id')->get();
        })->whereIn('status',[0,1])->orderBy('id')->get();
        if ($current_suggested->count() > 0)
        {
            return $current_suggested->pluck('submit_id')->toArray();
        }
        else{
            return [];
        }
    }

}

