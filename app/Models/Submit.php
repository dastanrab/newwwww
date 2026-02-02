<?php

namespace App\Models;

use App\Classes\BaleService;
use App\Classes\TransactionService;
use App\Events\ActivityEvent;
use App\RecordsActivity;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Kavenegar\KavenegarApi;

class Submit extends Model
{
    use HasFactory;
    use RecordsActivity;
    protected $fillable = [
        'fava_id', 'start_deadline', 'end_deadline', 'status', 'recyclables', 'total_amount'/*Toman*/ , 'final_amount', /*Toman*/ 'star', 'comment', 'survey', 'cancel', 'submit_phone', 'cashout_type', 'cashout_instant', 'canceled_at', 'canceller_id', 'registrant_id','region_id'
    ];

    protected $casts = [
        'recyclables' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canceller()
    {
        return $this->belongsTo(User::class,'canceller_id', 'id');
    }

    public function registrant()
    {
        return $this->belongsTo(User::class,'registrant_id', 'id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class)->withTrashed();
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }
    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    public function messages()
    {
        return $this->hasMany(SubmitMessage::class);
    }

    public function iban()
    {
        return $this->belongsTo(Iban::class);
    }

    public static function statusList()
    {
        return [
            [
                'value' => '1',
                'label' => 'در انتظار',
            ],
            [
                'value' => '2',
                'label' => 'در انتظار جمع آوری',
            ],
            [
                'value' => '3',
                'label' => 'جمع آوری شده',
            ],
            [
                'value' => '4',
                'label' => 'لغو توسط اپراتور',
            ],
            [
                'value' => '5',
                'label' => 'لغو توسط کاربر',
            ]
        ];
    }

    public static function statusInfo($statusKey = '') {
        $data = collect(Self::statusList())->pluck('label','value')->toArray();
        if (!empty($statusKey))
            return $data[$statusKey];
        else
            return $data;
    }

    public function status($key = '') {
        $data = (object)[
            'value' => $this->status,
            'label' => Self::statusInfo($this->status)
        ];
        if(empty($key)) {
            return $data;
        }
        else{
            return $data->$key;
        }
    }

    public static function driverStatusList()
    {
        return [
            [
                'value' => '1',
                'label' => 'در انتظار',
            ],
            [
                'value' => '2',
                'label' => 'فعال',
            ],
            [
                'value' => '3',
                'label' => 'جمع آوری شده',
            ],
            [
                'value' => '4',
                'label' => 'لغو توسط اپراتور',
            ],
            [
                'value' => '5',
                'label' => 'لغو توسط کاربر',
            ]
        ];
    }

    public static function driverStatusInfo($statusKey = '') {
        $data = collect(Self::driverStatusList())->pluck('label','value')->toArray();
        if (!empty($statusKey))
            return $data[$statusKey];
        else
            return $data;
    }

    public function driverStatus($key = '') {
        // #3498db => blue
        // #27ae60 => green
        // #e7cb3c => yellow
        // #f4500f => red
        if($this->status == 2){
            $color = '27ae60'; //green
        }
        elseif($this->status == 1 && $this->user->submits()->where('status', 3)->count() == 0){
            $color = 'e7cb3c'; //yellow
        }
        elseif($this->status == 1 && $this->is_instant){
            $color = 'f4500f'; //red
        }
        elseif ($this->status == 1 && $this->start_deadline <= now()){
            $color = 'f4500f'; //red
        }
        elseif ($this->status == 1){
            $color = '3498db'; //blue
        }
        else{
            $color = '000000'; //black
        }

        $data = (object)[
            'value' => $this->status,
            'label' => Self::driverStatusInfo($this->status),
            'color' => $color
        ];
        if(empty($key)) {
            return $data;
        }
        else{
            return $data->$key;
        }
    }

    public static function scheduleValidation($addressId,$day,$hour)
    {
        $user = auth()->user();
        $dayOfWeek = Day::find(verta()->parse($day)->format('w')+1);
        $hourOfDay = Hour::where('start_at',$hour)->first();
        $address = Address::find($addressId);
        $district = xDistrict([$address->lat,$address->lon]);
        $polygon = Polygon::where('region',$district)->first();
        $polygonDayHours = PolygonDayHour::where('city_id',$user->city_id)->where('polygon_id',$polygon->id)->where('day_id',$dayOfWeek->id)->where('hour_id',$hourOfDay->id)->first();
        if(!$polygonDayHours->status){
            return false;
        }
        elseif(verta()->parse($day)->isToday() && verta()->format('G') >= $hour){
            return false;
        }
        return true;
    }

    public static function immediateRange()
    {
        return [9,17];
    }

    public static function immediateValidate($hour)
    {
        if(Self::immediateRange()[0] <= $hour && Self::immediateRange()[1] > $hour){
            return true;
        }
        return false;
    }

    public static function driverList($request)
    {
        $user = auth()->user();
        $wasteItems = [];
        $totalPrice = 0;
        if($request->driver && $request->driver->receives) {
            foreach ($request->driver->receives as $receive) {
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
        }

        $messagesList = [];
        foreach ($request->messages as $message) {
            $messagesList[] = [
                'name'    => $message->user_id == $user->id ? 'من' : 'اپراتور',
                'self'    => $message->user_id == $user->id ?? false,
                'message' => $message->text,
            ];
        }


        $data = [
            'id' => $request->id,
            'status' => $request->driverStatus(),
            'mob' => $request->user->mobile,
            'location' => [
                'lat' => $request->address->lat,
                'lng' => $request->address->lon,
            ],
            'name'     => $request->user->name.' '.$request->user->lastname,
            'address'  => $request->address->address,
            'userType' => $request->user->getLegalName(),
            'wastes'   => [
                'items' => $totalPrice ? $wasteItems: null,
                'totalPrice' => $totalPrice ? floor($totalPrice) : null
            ],
            'date'    => [
                'day' => verta()->instance($request->start_deadline)->format('d F'),
                'time' => verta()->instance($request->start_deadline)->format('G:i').' الی '.verta()->instance($request->end_deadline)->format('G:i'),
            ],
            'messages' => [
                'badgeCount' => $request->messages->where('user_id', '!=', $user->id)->where('driver_seen',0)->count(),
                'list' => $messagesList
            ],
            'immediate' => $request->is_instant ? true : false,
            'firstRequest' => $request->user->submits()->where('status', 3)->count() == 0 ? true : false,
        ];

        return $data;
    }

    public static function driverCurrentList($request,$user)
    {
        $messagesList = [];
        foreach ($request->submit->messages as $message) {
            $messagesList[] = [
                'name'    => $message->user_id == $user->id ? 'من' : 'اپراتور',
                'self'    => $message->user_id == $user->id ?? false,
                'message' => $message->text,
            ];
        }

        $wasteItems = [];
        $totalPrice = 0;
        if($request->receives) {
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
        }

        $data = [
            'id' => $request->submit->id,
            'name' => $request->submit->user->name.' '.$request->submit->user->lastname,
            'mob' => $request->submit->user->mobile,
            'address' => $request->submit->address->address,
            'date' => [
                'day' => verta()->instance($request->submit->start_deadline)->format('d F'),
                'time' => verta()->instance($request->submit->start_deadline)->format('G:i').' الی '.verta()->instance($request->submit->end_deadline)->format('G:i'),
            ],
            'location' => [
                'lat' => $request->submit->address->lat,
                'lng' => $request->submit->address->lon
            ],
            'messages' => [
                'badgeCount' => $request->submit->messages->where('user_id', '!=', $user->id)->where('driver_seen',0)->count(),
                'list' => $messagesList
            ],
            'wastes'   => [
                'items' => $totalPrice ? $wasteItems: null,
                'totalPrice' => $totalPrice ? floor($totalPrice) : null
            ],
            'userType' => $request->submit->user->getLegalName(),
            'firstRequest' => $request->submit->user->submits()->where('status', 3)->count() == 0 ? true : false,
            'status' => $request->submit->driverStatus(),
        ];
        return $data;
    }

    public function payForUser()
    {
        $submit = $this;
        $user = $submit->user;
        $driver = $submit->driver;
        $wallet = Wallet::where('user_id', $user->id)->first();
        $type = $submit->submit_phone ? 'submit_phone' : 'submit';
        $userRRN = null;
        if($submit->cashout_type == 'card'){
            try {
                DB::transaction(function () use($user,$wallet,$submit,$type,$driver){
                    if($submit->iban) {
                        $iban = $submit->iban;
                        $cashout = new Cashout;
                        $cashout->user_id = $user->id;
                        $cashout->name = $iban->name;
                        $cashout->amount = $submit->total_amount ;
                        $cashout->card_number = $iban->card;
                        $cashout->shaba_number = str_replace('IR', '', $submit->iban->iban);
                        $cashout->status = 'waiting';
                        $cashout->save();
                        $final_amount = $submit->total_amount;
                        WalletDetails::create($submit->city_id, $user->id, $wallet->id, $type, $driver->id, $submit->total_amount * 10, ($wallet->wallet+$submit->total_amount) * 10, 'واریز', 'تحویل پسماند');
                        WalletDetails::create($submit->city_id, $user->id, $wallet->id, $type, $driver->id, $final_amount * 10, ($wallet->wallet+$submit->total_amount-$final_amount)*10, 'برداشت', 'واریز به حساب بانکی');
//                        BazistWallet::create($submit->city_id, $user->id, $wallet->id, 'cashout', $driver->id, wageRial(), ($wallet->wallet+$submit->total_amount-$final_amount-wageToman())*10 , 'برداشت', 'کارمزد واریز به حساب بانکی');
                    }
                    else{
                        //todo add transaction
                        $wallet->wallet = $wallet->wallet + $submit->total_amount;
                        $wallet->save();
                        WalletDetails::create($submit->city_id, $user->id, $wallet->id, $type, $driver->id, $submit->total_amount * 10, $wallet->wallet * 10, 'واریز', 'تحویل پسماند');
                    }
                });
            }
            catch (Exception $e){
                try {
                    $log = fopen("/home/laravel/la.bazistco.com/storage/logs/UserPayError.txt", "a+") or die("Unable to open file!");
                    $t = 'error|'.$e->getMessage()."\n";
                    fwrite($log, $t);
                    \App\Jobs\SendErrorLog::dispatch(['class_name'=>'UserPayError','message'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine(),'trace'=>substr($e->getTraceAsString(), 0, 100)])->onQueue('redis-queue');
                } catch (\Exception $e) {

                }
            }

        }
        elseif($submit->cashout_type == 'aap'){
            // اگر از این کد می خواهید استفاده کنید لطفا کارمزد را هم بهش اضافه کنید
            /*try {
                $userRRN = DB::transaction(function () use($user,$submit,$driver){
                    $userRRN = null;
                    $user_pay = AsanPardakht::increase($user->mobile, intval($submit->total_amount * 10));
                    $user_hresp = json_decode($user_pay['hresp']);
                    if($user_hresp->st == 0) {
                        $user_varify = AsanPardakht::verify($user_hresp->htran, $user_hresp->htime, $user_hresp->ao, $user_hresp->stkn);
                        $user_varify_hresp = json_decode($user_varify['hresp']);
                        if($user_varify_hresp->st == 0){
                            AsanPardakht::submitUserRecord($user->id,$driver,$user_hresp);
                            $userRRN = $user_hresp->rrn;
                        }
                    }
                    return $userRRN;
                });
            }
            catch (Exception $e){

            }*/
        }
        elseif($submit->cashout_type == 'bazist'){
            try {
                DB::transaction(function () use($user,$wallet,$submit,$type,$driver){
                    $wallet->wallet = $wallet->wallet + $submit->total_amount;
                    $wallet->save();
                    //todo add transaction
                    WalletDetails::create($submit->city_id, $user->id, $wallet->id, $type, $driver->id, $submit->total_amount * 10, $wallet->wallet * 10, 'واریز', 'تحویل پسماند');
                    create_transaction(0,$submit->user->id,$submit->total_amount,TransactionService::BAZIST_TYPE,TransactionService::BAZIST_TYPE,TransactionService::WASTE_RREASON,$submit->id);
                });
            }
            catch (Exception $e){
                try {
                    $data=[ 'class_name'=>get_class($e),'message'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine(),'trace'=>substr($e->getTraceAsString(), 0, 100)];
                    $bale=new BaleService();
                    $bale->SubmitPayError($data);
                }catch (Exception $e){

                }

            }
        }
        return $userRRN;
    }
    public function add_waste_score()
    {
        $submit=$this;
        $user = $submit->user;
        if ($submit->total_amount>0)
        {
            $score=$submit->total_amount/1000;
            if ($score>1)
            {
                try {
                    DB::beginTransaction();
                    $score=floor($score);
                    $user_score=User::query()->where('id', $user->id)->first();
                    ScoreHistory::query()->create(['user_id' => $user->id,'score'=>$score,'type'=>'granted','detail'=>"اعطا امتیاز جهت درخواست با شناسه {$submit->id}"]);
                    $user_score->score =$user_score->score+$score;
                    $user_score->save();
                    DB::commit();
                }catch (Exception $e){
                    DB::rollBack();
                }
            }
        }
    }
    public function firstSubmitUser()
    {
        $submit = $this;
        DB::transaction(function () use ($submit) {
            $user_submits = Submit::where('user_id', $submit->user->id)->where('status', 3)->count();
            if ($user_submits == 1 && $submit->submit_phone == 0) {
                $wallet = Wallet::where('user_id', $submit->user->id)->first();
                $wallet->wallet = $wallet->wallet + userRewardToman();
                $wallet->save();
                WalletDetails::create($submit->city_id, $submit->user->id, $wallet->id, 'first_submit_user', $submit->driver->id, userRewardRial(), $wallet->wallet * 10, 'واریز', 'پاداش اولین درخواست موفق');
            }
        });
    }

    public function rewardForReferral()
    {
        $submit = $this;
        try {
            DB::transaction(function () use ($submit){
                $referrer = Referrer::where('referrer_id', $submit->user->id)->first();
                if ($referrer) {
                    if ($referrer->rewarded_at == null) {
                        $submits = Submit::where('user_id', $submit->user->id)->where('status', 3)->with('drivers.receives')->get();
                        if ($submits->count() == 1) {
                            $user_ref = User::find($referrer->user_id);
                            $sum_weight = $submits[0]->drivers[0]->receives->pluck('weight')->sum();
                            if ($sum_weight >= 10) {
                                $walletRef = Wallet::where('user_id', $user_ref->id)->first();
                                $walletRef->wallet = $walletRef->wallet + referrerRewardToman();
                                $save = $walletRef->save();
                                if($save) {
                                    WalletDetails::create($submit->city_id, $user_ref->id, $walletRef->id, 'submit_user_ref', $submit->driver->id, referrerRewardRial(), $walletRef->wallet * 10, 'واریز', 'پاداش معرف');
                                }
                            }
                            if ($sum_weight >= 50) {
                                $car = Car::where('user_id', $user_ref->id)->where('is_active', 1)->first();
                                if ($car) {
                                    $walletRef = Wallet::where('user_id', $user_ref->id)->first();
                                    $walletRef->wallet = $walletRef->wallet + referrerRewardAbove50KiloToman();
                                    $save = $walletRef->save();
                                    if($save) {
                                        WalletDetails::create($submit->city_id, $user_ref->id, $walletRef->id, 'submit_user_ref', $submit->driver->id, referrerRewardAbove50KiloRial(), $walletRef->wallet * 10, 'واریز', 'پاداش معرف');
                                    }
                                }
                            }
                        }
                    }
                }
            });
        }catch (Exception $exception)
        {
            event(new ActivityEvent($exception->getMessage(), 'rewardForReferral', false));
        }

    }

    public function sendCollectedSms()
    {
        $submit = $this;
        $k = new KavenegarApi(env('KAVENEGAR_API_KEY'));
        $total_weights = $submit->driver->receives->pluck('weight')->sum();
        return $k->VerifyLookup($submit->user->mobile, $total_weights, number_format(floor($submit->total_amount)), '', 'SubmitInfo', 'sms');
    }

    public function decreaseDriverWallet($driverWallet)
    {
        $submit = $this;
        if ($driverWallet) {
            $driverWallet->amount -= (int)$submit->total_amount * 10;
            $driverWallet->save();
        }
    }

    public static function schedule($user,$address)
    {
        $district = xDistrict([$address->lat,$address->lon]);

        $polygonDayHours = PolygonDayHour::all();
        $polygon = Polygon::where('region',$district)->first();
        if(!$polygon){
            return ['status' => 'error', 'message' => 'شما خارج از محدوده هستید'];
        }
        $day = Day::all();
        $hour = Hour::all();
        //$titleDays = ['امروز','فردا','پسفردا','',''];

        for ($i=0;$i<5;$i++){
            $h9  = true;
            $h11 = true;
            $h13 = true;
            $h15 = true;
            $h17 = true;

            if(verta()->format('w')+$i+1 > 7){
                $dayId  = (verta()->format('w')+$i+1)-7;
            }
            else{
                $dayId = verta()->format('w')+$i+1;
            }

            if($i == 0 && verta()->format('G') >= 9){
                $h9 = false;
            }
            elseif(!$polygonDayHours
                ->where('city_id',$user->city->id)
                ->where('polygon_id',$polygon->id)
                ->where('day_id',$day->where('id',$dayId)->first()->id)
                ->where('hour_id',$hour->where('start_at',9)->first()->id)
                ->first()->status){
                $h9 = false;
            }
            if($i == 0 && verta()->format('G') >= 11){
                $h11 = false;
            }
            elseif(!$polygonDayHours
                ->where('city_id',1)
                ->where('polygon_id',$polygon->id)
                ->where('day_id',$day->where('id',$dayId)->first()->id)
                ->where('hour_id',$hour->where('start_at',11)->first()->id)
                ->first()->status){
                $h11 = false;
            }
            if($i == 0 && verta()->format('G') >= 13){
                $h13 = false;
            }
            elseif(!$polygonDayHours
                ->where('city_id',1)
                ->where('polygon_id',$polygon->id)
                ->where('day_id',$day->where('id',$dayId)->first()->id)
                ->where('hour_id',$hour->where('start_at',13)->first()->id)
                ->first()->status){
                $h13 = false;
            }
            if($i == 0 && verta()->format('G') >= 15){
                $h15 = false;
            }
            elseif(!$polygonDayHours
                ->where('city_id',1)
                ->where('polygon_id',$polygon->id)
                ->where('day_id',$day->where('id',$dayId)->first()->id)
                ->where('hour_id',$hour->where('start_at',15)->first()->id)
                ->first()->status){
                $h15 = false;
            }
            if($i == 0 && verta()->format('G') >= 17){
                $h17 = false;
            }
            elseif(!$polygonDayHours
                ->where('city_id',1)
                ->where('polygon_id',$polygon->id)
                ->where('day_id',$day->where('id',$dayId)->first()->id)
                ->where('hour_id',$hour->where('start_at',17)->first()->id)
                ->first()->status){
                $h17 = false;
            }
            if($i == 0){
                $weekday = 'امروز';
            }
            elseif($i == 1){
                $weekday = 'فردا';
            }
            elseif($i == 2){
                $weekday = 'پسفردا';
            }
            else{
                $weekday = verta()->addDays($i)->format('l');
            }

            $data['list'][] = [
                'value' => verta()->addDays($i)->format('Y/m/d'),
                'label' => verta()->addDays($i)->format('Y/m/d'),
                'subLabel' => /*$titleDays[$i]*/'',
                'weekday' => $weekday,
                'enabled' => $h9 || $h11 || $h13 || $h15 || $h17,
                'hours' => [
                    [
                        'value' => '9',
                        'label' => '9 الی 12',
                        'subLabel' => 'صبح',
                        'enabled' => $h9
                    ],
                    [
                        'value' => '11',
                        'label' => '11 الی 14',
                        'subLabel' => 'ظهر',
                        'enabled' => $h11
                    ],
                    [
                        'value' => '13',
                        'label' => '13 الی 16',
                        'subLabel' => 'عصر',
                        'enabled' => $h13
                    ],
                    [
                        'value' => '15',
                        'label' => '15 الی 18',
                        'subLabel' => 'شب',
                        'enabled' => $h15
                    ],
                    [
                        'value' => '17',
                        'label' => '17 الی 20',
                        'subLabel' => 'شب',
                        'enabled' => $h17
                    ]
                ],
            ];
        }
        if (isset($polygon->has_instant) and $polygon->has_instant == 1)
        {
            $user_type=auth()->user()->legal;
            if ($user_type == 0){
                $data['immediate']= (isset($polygon->has_legal_collect) and $polygon->has_legal_collect == 1);
            }
            else{
                $data['immediate']= (isset($polygon->has_illegal_collect) and $polygon->has_illegal_collect == 1);

            }
        }else{
            $data['immediate']=false;
        }
        return $data;
    }

    public static function add($registrantId,$user,$request)
    {
        $payMethod = !isset($request->payMethod) ? 'bazist' : null;

        if($request->scheduling == 'immediate'){
            $start_deadline = now()->format('Y-m-d H:i:s');
            $end_deadline = now()->addHour()->format('Y-m-d H:i:s');
            $is_instant = 1;
        }
        else{
            $start_deadline = verta()->parse($request->scheduling['day'])->addHours($request->scheduling['hour'])->toCarbon();
            $end_deadline = verta()->parse($request->scheduling['day'])->addHours($request->scheduling['hour']+3)->toCarbon();
            $is_instant = 0;
        }
        $address = Address::find($request->addressId);
        $district = getAddressRegion([$address->lat,$address->lon]);
        $submit = new Submit;
        $submit->registrant_id = $registrantId;
        $submit->user_id = $user->id;
        $submit->start_deadline = $start_deadline;
        $submit->end_deadline = $end_deadline;
        $submit->is_instant = $is_instant;
        $submit->recyclables = json_encode(['GoodRef' => 1, 'Quantity' => 1, 'Price' => Percentage::where('recyclable_id', 1)->where('is_legal', false)->where('weight', 1)->first()->price * 10]);

        $city_id = $address->city_id;
        $submit->address_id = $address->id;
        $submit->region_id= @$district;
        $submit->city_id = $city_id;
        $submit->cashout_type = $payMethod;
        $submit->is_instant = $request->scheduling == 'immediate' ? 1 : 0;
        $submit->cashout_instant = 0;
        $submit->submit_phone = 0;
        $submit->save();

        $archive_id = ReceiveArchive::new($submit);
        if ($user->legal) {
            ArchiveLegal::new($submit, $archive_id);
        } else {
            ArchiveNotLegal::new($submit, $archive_id);
        }

        if ($submit->submit_phone) {
            ArchivePhone::new($submit, $archive_id);
        } else {
            ArchiveApp::new($submit, $archive_id);
        }
        return $submit;
    }

    public static function mapSettings($key = null)
    {
        $data = [
            'instant'  => [
                'navColor' => '#e74c3c',
                'color' => '#ee7367',
                'outlineColor' => '#e74c3c',
                'icon' => 'directions_run',
            ],
            '9'  => [
                'navColor' => '#F97F51',
                'color' => '#fe9871',
                'outlineColor' => '#F97F51',
                'icon' => 'emoji_people',
            ],
            '11' => [
                'navColor' => '#f1c40f',
                'color' => '#f1c40f',
                'outlineColor' => '#ffdd54',
                'icon' => 'emoji_people',
            ],
            '13' => [
                'navColor' => '#27ae60',
                'color' => '#4dde8a',
                'outlineColor' => '#27ae60',
                'icon' => 'emoji_people',
            ],
            '15' => [
                'navColor' => '#76381e',
                'color' => '#985437',
                'outlineColor' => '#76381e',
                'icon' => 'emoji_people',
            ],
            '17' => [
                'navColor' => '#82589F',
                'color' => '#8f72a3',
                'outlineColor' => '#82589F',
                'icon' => 'emoji_people',
            ],
            'active' => [
                'navColor' => '#63cdda',
                'color' => '#99d1d8',
                'outlineColor' => '#63cdda',
                'icon' => 'boy',
            ],
            'first' => [
                'navColor' => '#FC427B',
                'color' => '#FC427B',
                'outlineColor' => '#f8769e',
                'icon' => 'sentiment_very_satisfied',
            ],
            'done' => [
                'navColor' => '#2C3A47',
                'color' => '#4b5257',
                'outlineColor' => '#2C3A47',
                'icon' => 'airline_seat_recline_extra',
            ],
            'driver' => [
                'navColor' => '#f1c40f',
                'color' => '#f1c40f',
                'outlineColor' => '#ffdd54',
                'icon' => 'directions_car',
            ],
        ];
        if(!empty($key)){
            return collect($data[$key]);
        }
        return collect($data);
    }
    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }


}
