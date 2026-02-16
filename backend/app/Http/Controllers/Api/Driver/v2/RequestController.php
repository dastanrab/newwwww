<?php

namespace App\Http\Controllers\Api\Driver\v2;

use App\Classes\RequestSuggestionV2;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessPayment;
use App\Models\Address;
use App\Models\ArchiveApp;
use App\Models\ArchiveLegal;
use App\Models\ArchiveNotLegal;
use App\Models\ArchivePhone;
use App\Models\Car;
use App\Models\Driver;
use App\Models\DriverSuggestedRequests;
use App\Models\DriverWallet;
use App\Models\Location;
use App\Models\Polygon;
use App\Models\ReceiveArchive;
use App\Models\RecyclableHistory;
use App\Models\Submit;
use App\Models\User;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    private RequestSuggestionV2 $suggest_class;


    public function map(Request $request)
    {
        $user = auth()->user();
        $nearest_requests=[];
        $car = Car::query()->where('user_id',$user->id)->first();
        $location=Location::query()->where('car_id',$car->id)->whereDate('created_at',now())->latest()->first();
        if ($location)
        {
            $this->suggest_class=new RequestSuggestionV2($user->id);
            $nearest_requests=$this->suggest_class->driver_suggesteds_v2();
            $nearest_requests=count($nearest_requests)>0?array_slice($nearest_requests,0,3):$nearest_requests;
        }
        $polygons = Polygon::whereIn('id',$user->polygonDrivers->pluck('polygon_id'))->pluck('region')->toArray();
        $submitsPending = Submit::where('status', 1)->whereDate('start_deadline',now())
            ->with(['user', 'address' => function ($query) {
                $query->withTrashed();
            }])->get();
        $filtered = [];
        foreach ($submitsPending as $item) {
            if(in_array(xDistrict([$item->address->lat,$item->address->lon]),$polygons)){
                $filtered[] = $item;
            }
        }
        $submitsActive = Submit::where('status', 2)->whereDate('end_deadline', now())
            ->with(['user', 'address' => function ($query) {
                $query->withTrashed();
            }])
            ->whereHas('drivers', function ($query) use($user){
                $query->where('user_id',$user->id);
            })
            ->get();
        $list = [];
        foreach ($filtered as $item){
            $filter=Submit::driverList($item);
            $filter['enable']=in_array($filter['id'],$nearest_requests);
            $filter['first']= false;
            $filter['second']=false;
            $filter['third']=false;
            if (isset($nearest_requests[0]))
            {
                $filter['first']= $filter['id'] == $nearest_requests[0];
            }
            if (isset($nearest_requests[1]))
            {
                $filter['second']= $filter['id'] == $nearest_requests[1];
            }
            if (isset($nearest_requests[2]))
            {
                $filter['third']= $filter['id'] == $nearest_requests[2];
            }
            $list[] = $filter;
        }
        foreach ($submitsActive as $item){
            $filter = Submit::driverList($item);
            $filter['enable']=in_array($filter['id'],$nearest_requests);
            $filter['first']= false;
            $filter['second']=false;
            $filter['third']=false;
            if (isset($nearest_requests[0]))
            {
                $filter['first']= $filter['id'] == $nearest_requests[0];
            }
            if (isset($nearest_requests[1]))
            {
                $filter['second']= $filter['id'] == $nearest_requests[1];
            }
            if (isset($nearest_requests[2]))
            {
                $filter['third']= $filter['id'] == $nearest_requests[2];
            }
            $list[]=$filter;
        }
        return sendJson('success','',$list);
    }
    public function receive(Submit $submit)
    {
        $user = auth()->user();
        $suggested=DriverSuggestedRequests::query()->where('submit_id',$submit->id)->where('driver_id',$user->id)->where('status',0)->first();
        if (!$suggested)
        {
            return sendJson('error','این درخواست از درخواست های پیشنهادی شما نیست و مجاز به قبول آن نیستید');
        }
        if ($user->drivers()->where('status', 2)->count() >= 4 && $user->id !== 2) {
            return sendJson('error','نمی توانید بیشتر از ۴ درخواست فعال داشته باشید');
        }
        elseif (!isLocationInsidePolygon($user->id,[$submit->address->lat,$submit->address->lon])){
            return sendJson('error','این درخواست خارج از منطقه شما می باشد');
        }
        elseif($submit->status > 1){
            return sendJson('error','متاسفانه درخواست در وضعیتی نمی باشد که آن را دریافت کنید');
        }
        elseif(isset($submit->driver->id)){
            return sendJson('error','این درخواست توسط شما انتخاب شده، لطفا اپلیکیشن را بسته و دوباره باز کنید');
        }
        DriverSuggestedRequests::query()->where('submit_id',$submit->id)->whereNot('driver_id',$user->id)->delete();
        DriverSuggestedRequests::query()->where('submit_id',$submit->id)->where('driver_id',$user->id)->update(['status'=>1]);
        $saveDriver = Driver::add($user,$submit);
        //TODO add transaction
        if($saveDriver){
            $data = Submit::driverList($submit);
//            $attendance=DriversAttendanceLogs::query()->where('user_id',$user->id)->whereDate('start_at',now())->whereNull('end_at')->first();
//            if ($attendance and \Carbon\Carbon::parse($submit->start_dedline)->isToday())
//            {
//                $end=\Carbon\Carbon::now();
//                $start=new \Carbon\Carbon($attendance->start_at);
//                $attendance->update(['end_at'=>now(),'time_length'=>$start->diffInMinutes($end),'submit_id'=>$submit->id]);
//            }
            return sendJson('success','درخواست به شما تعلق گرفت', $data);
        }
        else{
            return sendJson('error','اشکالی پیش آمد لطفا دوباره تلاش کنید');
        }
    }
    public function done(Submit $submit)
    {
        $user = auth()->user();
        $driver = $submit->driver;
        if ($driver->user_id != $user->id) {
            return sendJson('error','این درخواست متعلق به شما نمی باشد');
        }
        elseif($submit->status != 2){
            return sendJson('error','درخواست در وضعیت اتمام قرار ندارد');
        }
        elseif($driver->receives->count() == 0){
            return sendJson('error','برای این درخواست هنوز پسماندی ثبت نشده است');
        }
        $driverWallet = DriverWallet::where('user_id', $driver->user_id)->first();
        if ($driverWallet && $driverWallet->amount < $submit->total_amount * 10
        ) {
            return sendJson('error','موجودی کیف پول شما کمتر از مبلغ درخواست است');
        }

        $requester = User::find($submit->user_id);
        DriverSuggestedRequests::query()->where('driver_id',$user->id)->where('submit_id',$submit->id)->update(['status'=>2]);
        $submit->update(['status' => 3]);
        $total_weights = $driver->receives->pluck('weight')->sum();
        $driver->update(['status' => 3, 'collected_at' => now(), 'weights' => $total_weights, 'city_id' => $submit->city_id]);
//        $attendance=DriversAttendanceLogs::query()->where('user_id',$driver->user_id)->whereDate('start_at',now())->whereNull('end_at')->first();
//        if ($attendance)
//        {
//            $end=\Carbon\Carbon::now();
//            $start=new \Carbon\Carbon($attendance->start_at);
//            $attendance->update(['end_at'=>now(),'time_length'=>$start->diffInMinutes($end),'submit_id'=>$submit->id]);
//        }
        $submit->total_amount = round($submit->total_amount, 1);
        $total_price = 0;
        foreach ($submit->driver->receives as $receive){
            $total_price += RecyclableHistory::whereDate('created_at', '<=', $submit->start_deadline)->latest()->pluck($receive->fava_id)->first() * $receive->weight;
        }
        $submit->final_amount = $total_price;
        $submit->save();


        ReceiveArchive::add($driver);
        if ($submit->user->legal) {
            ArchiveLegal::add($driver);
        } else {
            ArchiveNotLegal::add($driver);
        }
        if ($submit->submit_phone) {
            ArchivePhone::add($driver);
        } else {
            ArchiveApp::add($driver);
        }
        $userRRN = $submit->payForUser();
        $submit->decreaseDriverWallet($driverWallet);
      //  $submit->sendCollectedSms();
        $submit->firstSubmitUser();
        $submit->rewardForReferral();
        $submit->add_waste_score();
        ProcessPayment::dispatch($requester, $submit,$userRRN);
        return sendJson('success','با موفقیت انجام شد',[]);
    }
}
