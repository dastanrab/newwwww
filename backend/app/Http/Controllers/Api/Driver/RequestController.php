<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPayment;
use App\Models\ArchiveApp;
use App\Models\ArchiveLegal;
use App\Models\ArchiveNotLegal;
use App\Models\ArchivePhone;
use App\Models\AsanPardakht;
use App\Models\WalletDetails;
use App\Models\Car;
use App\Models\Cashout;
use App\Models\Driver;
use App\Models\DriversAttendanceLogs;
use App\Models\DriverWallet;
use App\Models\Fava;
use App\Models\Firebase;
use App\Models\Location;
use App\Models\Percentage;
use App\Models\Polygon;
use App\Models\ReceiveArchive;
use App\Models\RecyclableHistory;
use App\Models\Submit;
use App\Models\SubmitMessage;
use App\Models\User;
use App\Models\Receive;
use App\Models\Recyclable;
use App\Models\Wallet;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Kavenegar\KavenegarApi;

class RequestController extends Controller
{

    public function map(Request $request)
    {
        $user = auth()->user();
        $polygons = Polygon::whereIn('id',$user->polygonDrivers->pluck('polygon_id'))->pluck('region')->toArray();
        $requests = [];
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
            $list[] = Submit::driverList($item);
        }
        foreach ($submitsActive as $item){
            $list[] = Submit::driverList($item);
        }

        if($request->location){
            $location = new Location;
            $location->car_id = $user->car->id;
            $location->lat = $request->location['lat'];
            $location->long = $request->location['lng'];
            $location->date = now();
            $location->save();
        }

        return sendJson('success','',$list);
    }

    public function currentList()
    {
        $user = auth()->user();
        $paginate = 10;
        $data = ['list' => [], 'limit' => $paginate];
        $requests = Driver::where('drivers.user_id', $user->id)->where('drivers.status', 2)
            ->join('submits', 'drivers.submit_id', '=', 'submits.id')
            ->orderBy('submits.end_deadline', 'asc')
            ->select('drivers.*')
            ->with(['submit.address' => function ($query) {
                $query->withTrashed();
            }])
            ->paginate($paginate);
        foreach ($requests as $request){
            $data['list'][] = Submit::driverCurrentList($request,$user);
        }
        return sendJson('success','',$data);

    }

    public function storeMessage(Request $request, Submit $submit)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
                'id' => 'required|integer',
            ],
            [
                'id'    => 'آی دی پیام را وارد نمایید',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        elseif($submit->drivers()->first()->user_id != $user->id){
            return sendJson('error','این درخواست مربوط به شما نمی باشد و نمی توانید پیام ثبت کنید');
        }
        $messages = SubmitMessage::driverMessages();

        $save = $submit->messages()->create([
            'user_id' => $user->id,
            'text' => $messages[$request->id],
        ]);
        if($save){
            $submit->messages()->where('user_id','!=',$user->user_id)->where('driver_seen',0)->update(['driver_seen' => 1]);

            $messagesList = [];
            foreach ($submit->messages as $message) {
                $messagesList[] = [
                    'name'    => $message->user_id == $user->id ? 'من' : 'اپراتور',
                    'self'    => $message->user_id == $user->id ?? false,
                    'message' => $message->text,
                ];
            }

            $data = [
                'badgeCount' => $submit->messages->where('user_id', '!=', $user->id)->where('driver_seen',0)->count(),
                'list' => $messagesList
            ];
            return sendJson('success','با موفقیت ثبت شد',$data);
        }
        return sendJson('error','اشکال در ثبت پیام لطفا دوباره امتحان نمایید');
    }

    public function historyList()
    {
        $user = auth()->user();
        $paginate = 100;
        $data = ['list' => [], 'limit' => $paginate];
        $requests = Driver::where('drivers.user_id', $user->id)->where('drivers.status', 3)->whereDate('drivers.collected_at', now())
            ->join('submits', 'drivers.submit_id', '=', 'submits.id')
            ->orderBy('submits.end_deadline', 'asc')
            ->select('drivers.*')
            ->with(['submit.address' => function ($query) {
                $query->withTrashed();
            }])->paginate($paginate);
        foreach ($requests as $request){

            $wasteItems = [];
            $totalPrice = 0;
            foreach ($request->receives as $receive) {
                $totalPrice += $receive->price*$receive->weight;
                $wasteItems[] = [
                    'id' => $receive->id,
                    'type' => [
                        'value' => $receive->fava_id,
                        'label' => $receive->title,
                    ],
                    'image' => asset("assets/img/icons/recyclables/{$receive->fava_id}.png"),
                    'weight' => $receive->weight,
                    'price' => floor($receive->price),
                    'totalPrice' => floor($receive->price*$receive->weight),
                ];
            }

            $data['list'][] = [
                'id' => $request->submit->id,
                'name' => $request->submit->user->name.' '.$request->submit->user->lastname,
                'mob' => $request->submit->user->mobile,
                'address' => $request->submit->address->address,
                'totalWeight' => weightFormat($request->weights),
                'date' => [
                    'day' => verta()->instance($request->submit->start_deadline)->format('d F'),
                    'time' => verta()->instance($request->submit->start_deadline)->format('G:i').' الی '.verta()->instance($request->submit->end_deadline)->format('G:i'),
                ],
                'wastes' => [
                    'items' => $totalPrice ? $wasteItems: null,
                    'totalPrice' => $totalPrice ? floor($totalPrice) : null
                ],
                'userType' => $request->submit->user->getLegalName()
            ];
        }
        return sendJson('success','',$data);
    }

    public function receive(Submit $submit)
    {
        $user = auth()->user();
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

        $saveDriver = Driver::add($user,$submit);
        if($saveDriver){
            $data = Submit::driverList($submit);
            return sendJson('success','درخواست به شما تعلق گرفت', $data);
        }
        else{
            return sendJson('error','اشکالی پیش آمد لطفا دوباره تلاش کنید');
        }
    }

    public function storeWaste(Request $request, Submit $submit)
    {

        $user = auth()->user();
        //dd($submit->driver->receives->pluck('fava_id'));
        if ($submit->driver->user_id != $user->id) {
            return sendJson('error','این درخواست متعلق به شما نمی باشد');
        }
        elseif ($submit->status != 2){
            return sendJson('error','وضعیت درخواست در حالتی نمی باشد که پسماندی برای آن ثبت شود');
        }
        $validator = Validator::make($request->all(),
            [
                'id'     => 'required|exists:recyclables,id',
                'weight' => 'required|numeric',
            ],
            [
                'id'     => 'پسماند را به درستی انتخاب کنید',
                'weight' => 'وزن را به درستی وارد نمایید',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        $requester = $submit->user;
        $recyclable = Recyclable::find($request->id);
        $fava_price=RecyclableHistory::whereDate('created_at', '<=', $submit->start_deadline)->latest()->pluck($recyclable->id)->first();
        if($submit->driver->receives()->where('fava_id',$request->id)->count() > 0){
            return sendJson('error','این پسماند را قبلا ثبت کرده اید');
        }


        $originalPrice = Percentage::where('recyclable_id', $recyclable->id)->where('is_legal', $requester->legal)->where('weight', floor($request->weight))->first();
        if ($originalPrice) {
            $price = $originalPrice->price;
        } else {
            $order = 'desc';
            if ($request->weight <= 0) {
                $order = 'asc';
                $weight_request = $request->weight;
            } else if ($request->weight < 1) {
                $weight_request = 2;
            } else {
                $weight_request = $request->weight;
            }
            $originalPrice = Percentage::where('recyclable_id', $recyclable->id)->where('is_legal', $requester->legal)->orderBy('weight', $order)->where('weight', '<', $weight_request)->first()->price;
            $price = $originalPrice;
        }
        if ($request->price) {
            if($request->price < $price) {
                return ['result' => 0, 'type' => 'price', 'message' => "حداقل مبلغ وارد شده برای {$recyclable->title} {$price} تومان می باشد"];
            }
            $price = $request->price;
        }
//        if (in_array($user->id,weight_drivers()))
//        {
//            $price=weight_drivers_prices($recyclable->id);
//        }

        $receive = new Receive;
        $receive->driver_id = $submit->driver->id;
        $receive->title = $recyclable->title;
        $receive->fava_id = $recyclable->id;
        $receive->price = $price;
        $receive->fava_price = @$fava_price;
        $receive->weight = $request->weight;
        $save = $receive->save();
        if($save) {
            $submit->update(['total_amount' => $submit->total_amount + ($price * $request->weight)]);
            $data['items'] = [];
            $totalPrice = 0;
            foreach ($submit->driver->receives as $receive) {
                $totalPrice += $receive->price*$receive->weight;
                $data['items'][] = [
                    'id' => $receive->id,
                    'type' => [
                        'value' => $receive->fava_id,
                        'label' => $receive->title,
                    ],
                    'image' => asset("assets/img/icons/recyclables/{$receive->fava_id}.png"),
                    'weight' => $receive->weight,
                    'price' => floor($receive->price),
                    'totalPrice' => floor($receive->price*$receive->weight),
                ];
            }
            $data['totalPrice'] = floor($totalPrice);
            return sendJson('success','پسماند در پایگاه داده ثبت شد' , $data);
        }
        return sendJson('error','مشکلی در ثبت پسماند پیش آمد لطفا دوباره امتحان کنید');
    }

    public function destroyWaste(Receive $receive)
    {
        $user = auth()->user();
        if($user->id != $receive->driver->user->id){
            return sendJson('error','شما اجازه حذف پسماند را ندارید');
        }
        elseif ($receive->driver->status != 2){
            return sendJson('error','وضعیت درخواست در حالتی نمی باشد که پسماندی برای آن ثبت شود');
        }
        $driver = $receive->driver;
        $delete = $receive->delete();
        if($delete){

            $submit = $receive->driver->submit;
            $total = $submit->total_amount - $receive->price * $receive->weight;
            $submit->update(['total_amount' => $total]);

            $receives = $driver->receives;
            $totalPrice = 0;
            $wasteItems = [];
            if($receives) {
                foreach ($receives as $item) {
                    $totalPrice += $item->price*$item->weight;
                    $wasteItems[] = [
                        'id' => $item->id,
                        'type' => [
                            'value' => $item->fava_id,
                            'label' => $item->title,
                        ],
                        'image' => asset("assets/img/icons/recyclables/{$item->fava_id}.png"),
                        'weight' => $item->weight,
                        'price' => floor($item->price),
                        'totalPrice' => floor($item->price*$item->weight),
                    ];
                }
            }
            $data = [
                'items' => $totalPrice ? $wasteItems: null,
                'totalPrice' => $totalPrice ? floor($totalPrice) : null
            ];
            return sendJson('success','پسماند حذف گردید', $data);
        }
        else{
            return sendJson('error','حذف پسماند با اشکال روبرو شد');
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
        $submit->update(['status' => 3]);
        $total_weights = $driver->receives->pluck('weight')->sum();
        $driver->update(['status' => 3, 'collected_at' => now(), 'weights' => $total_weights, 'city_id' => $submit->city_id]);

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
       // $submit->sendCollectedSms();
        $submit->firstSubmitUser();
        $submit->rewardForReferral();
        $submit->add_waste_score();
        ProcessPayment::dispatch($requester, $submit,$userRRN);
        return sendJson('success','با موفقیت انجام شد',[]);
    }
    public function attendance()
    {
        $user=auth()->user();
        $attendance=DriversAttendanceLogs::query()->where('user_id',$user->id)->whereDate('start_at',now())->whereNull('end_at')->first();
        if ($attendance)
        {
            return sendJson('error','شما حاضر به کار باز دارید');
        }
        DriversAttendanceLogs::query()->create(['user_id'=>$user->id,'start_at'=>now()]);
        return sendJson('success','با موفقیت انجام شد',[]);
    }

}
