<?php

namespace App\Http\Controllers;

use App\Events\ActivityEvent;
use App\Models\Address;
use App\Models\AsanPardakht;
use App\Models\Car;
use App\Models\Cashout;
use App\Models\Driver;
use App\Models\Fava;
use App\Models\Inax;
use App\Models\Percentage;
use App\Models\Recyclable;
use App\Models\RecyclableHistory;
use App\Models\User;
use App\Models\Submit;
use App\Pakban;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavaController extends Controller
{
    public function updatePrice()
    {
        $fava = Fava::CurrentPricesService();
        if($fava && isset($fava->vwCurrentPrices)){
            $lastRecyclableHistory = RecyclableHistory::latest()->first();
            $latestRecyclable = Carbon::parse($lastRecyclableHistory->created_at);
            $favaLatest = Carbon::parse($fava->vwCurrentPrices[0]->StartDate);
            if($latestRecyclable < $favaLatest){
                foreach ($fava->vwCurrentPrices as $current_price) {
                    $recyclable = Recyclable::find($current_price->GoodRef);
                    if (isset($recyclable))
                    {
                        $recyclable->update(['price' => $current_price->Price / 10]);
                    }
                    else{
                        $log = fopen("/home/laravel/la.bazistco.com/storage/logs/FailRecycle.txt", "a+") or die("Unable to open file!");
                        $t = 'fail|'.$current_price->GoodRef."\n";
                        fwrite($log, $t);
                    }
                }
                $recyclables = Recyclable::all();
                DB::table('recyclable_histories')->insert(['1' => $recyclables[0]->price,'2'=> $recyclables[1]->price,'3' => $recyclables[2]->price,'4'=> $recyclables[3]->price,'5' => $recyclables[4]->price,'6'=> $recyclables[5]->price,'7' => $recyclables[6]->price,'8'=> $recyclables[7]->price, '9' => $recyclables[8]->price,'10'=> $recyclables[9]->price,'11' => $recyclables[10]->price,'12'=> $recyclables[11]->price,'13' => $recyclables[12]->price,'14'=> $recyclables[13]->price, '15' => $recyclables[14]->price,'16'=> $recyclables[15]->price,'17' => $recyclables[16]->price,'18'=> $recyclables[17]->price,'19' => $recyclables[18]->price, '20'=> $recyclables[19]->price, '21'=> $recyclables[20]->price,'22'=> @$recyclables[21]->price,'created_at'=>now(),'updated_at'=>now()]);

                $recyclables = Recyclable::all();
                foreach ($recyclables as $recyclable) {
                    $percentages = Percentage::where('recyclable_id', $recyclable->id)->get();
                    foreach ($percentages as $percentage) {
                        $percentage->update(['price' => ceil($recyclable->price * $percentage->percent * 0.01)]);
                    }
                }

                $myfile = fopen("/home/laravel/la.bazistco.com/storage/logs/updatePrice.txt", "a+") or die("Unable to open file!");
                $txt = 'updated|'.date('Y-m-d H:i:s')."\n";
                fwrite($myfile, $txt);

                echo 'Updated!';
            }
            else{
                $myfile = fopen("/home/laravel/la.bazistco.com/storage/logs/updatePrice.txt", "a+") or die("Unable to open file!");
                $txt = 'no updated|'.date('Y-m-d H:i:s')."\n";
                fwrite($myfile, $txt);
                echo 'no updated!';
            }
        }
    }


    public function favaReg()
    {
        // وقتی کاربری به هر دلیل به فاوا ارسال نشه از این طریق دستی به فاوا ارسال میشه
        $users = User::where('fava_id', null)->where('city_id', 1);
        if(auth()->id() == developerId()){
            dump($users->count());
        }
        $users = $users->offset(0)->take(5)->get();
        foreach ($users as $user) {
            $fava_id = Fava::GetUserIDService($user->mobile);
            if ($fava_id == -1) {
                $fava_id = Fava::insertCustomerService($user);
            }
            $user->fava_id = $fava_id;
            $user->save();
            echo $fava_id . ' - ' . $user->id . '<br>';
        }
        if(auth()->id() == developerId() && $users->count() > 0) {
            sleep(3);
            return redirect('/fava/reg');
        }
    }


    public function favaCar()
    {
        $cars = Car::where('fava_id', null);
        if(auth()->id() == developerId()) {
            dump($cars->count());
        }
        $cars = $cars->offset(0)->take(10)->get();
        foreach ($cars as $car) {
            $fava_id = Fava::GetCarIDService($car);
            if ($fava_id == -1) {
                $fava_id = Fava::InsertCarService($car);
            }
            $car->fava_id = $fava_id;
            $car->save();
            echo $fava_id . ' -- ' . $car->id . '<br>';
        }
        if(auth()->id() == developerId() && $cars->count() > 0) {
            sleep(3);
            return redirect('/fava/car');
        }

    }


    public function favaSubmit()
    {
        $numbers = [
            254057,
            257966,
            259265,
            261255,
            267854
        ];

        // زمانی که کاربر درخواست رو ثبت میکنه و به فاوا ارسال نمیشه با این اسکریپت هم چک میشه و هم ارسال میشه
        $submits = Submit::where('fava_id', null)->where('city_id', 1)->whereNotIn('id', $numbers);
        if(auth()->id() == developerId()) {
            dump($submits->count());
        }
        $submits = $submits->offset(0)->take(30)->get();
        foreach ($submits as $submit) {
            $fava_id = Fava::GetSubmitIDService($submit);
            usleep(500000); // نیم ثانیه
            if ($fava_id == -1) {
                $fava_id = Fava::insertRequestService($submit);
                usleep(500000); // نیم ثانیه
            }
            $submit->fava_id = $fava_id;
            $submit->save();
            echo $fava_id . ' - ' . $submit->id . '<br>';
        }
        if(auth()->id() == developerId() && $submits->count() > 0) {
//            sleep(3);
//            return redirect('/fava/submit');
        }

    }


    public function favaDriver()
    {

        $drivers = Driver::where('fava_id', null)->where('status', 3)->where('city_id', 1);
        if(auth()->id() == 67775) {
            dump($drivers->count());
        }
        //گرفتن درخواست هایی که ای دی فاوا ندارد
        $drivers = $drivers->take(30)->get();
        foreach ($drivers as $driver) {

            $fava_id = Fava::GetDriverIDService($driver);
            usleep(500000); // نیم ثانیه
            dump($fava_id);
            if ($fava_id == -1) { // اگر -۱ بود یعنی وجود ندارد و اون درخواست برای فاوا ارسال می کنیم
                $fava_id = Fava::insertCollectingService2($driver);
                usleep(500000); // نیم ثانیه

            }
            // اگر وجود داشت فاوا ای دی برمیگرده از سمت فاوا و اون رو در دیتابیس ثبت میکنیم
            $driver->fava_id = $fava_id;
            $driver->save();
            echo 'fava-id => '.$fava_id . ' - ' . $driver->id . '<br>';


        }
        //echo 'درخواست ها در ۴۰ تا سمت فاوا ارسال میشوند در صورت نیاز رفرش کنید';
        if(auth()->id() == developerId() && $drivers->count() > 0) {
            sleep(3);
         //   return redirect('/fava/driver');
        }
    }

    public function apiDaily(Request $request)
    {

        $date = $request->date;
        $token = $request->bearerToken();
        // $this->validate($request, [
        //     'date'    => 'jdate:Y/m/d'
        // ]);
        // $validator = Validator::make($request->all(), [
        //     'date' => 'required|jdate:Y-m-d',
        // ]);
        // if ($validator->fails()) {
        //     return $date;
        // }
        // $validated = $validator->validated();
        try {
            $date_en = Verta::parse($date)->formatGregorian('Y-m-d');
        } catch (\Throwable $th) {
            return sendJson('error','فرمت تاریخ به درستی وارد نشده است');
        }

        if ($token == env('FAVA_USER_TOKEN')) {
            $submits = Submit::whereDate('start_deadline', $date_en)->where('city_id', 1)->get();
            $pakbans = Driver::whereDate('collected_at', $date_en)->where('status',3)->where('city_id', 1)->with(['submit', 'receives'])->get();
            $collected = $pakbans->count();
            $canceled = $submits->where('status', 4)->count();
            $removed = $submits->where('status', 5)->count();
            $not_collected = $submits->where('status', 1)->count() + $submits->where('status', 2)->count();
            $total_price = 0;
            $user_share = 0;

            // foreach ($submits->where('status', 3) as $s) {
            //     foreach ($s->pakbans as $p) {
            //         foreach ($p->receives as $r) {
            //             $total_weight += $r->weight;
            //             $total_price += RecyclableHistory::whereDate('created_at', '<=', $s->start_deadline)->latest()->pluck($r->fava_id)->first() * $r->weight;
            //         }
            //     }
            // }

            foreach ($pakbans as $p) {
                foreach ($p->receives as $r) {
                    $total_price += RecyclableHistory::whereDate('created_at', '<=', $p->collected_at)->latest()->pluck($r->fava_id)->first() * $r->weight;
                }
                $user_share += $p->submit->total_amount * 10;
            }

            $municipality_share = AsanPardakht::whereDate('created_at', $date_en)->where('type', 'submit_fava')->pluck('amount')->sum();

            event(new ActivityEvent('Statistics Daily API', 'statistics', true));

            return [
                'date' => $date,
                'total_requests' => $collected + $not_collected + $canceled + $removed,
                'collected' => $collected,
                'not_collected' => $not_collected,
                'canceled' => $canceled,
                'removed' => $removed,
                'weight' => round($pakbans->pluck('weights')->sum(), 2),
                'value' => $submits->sum('final_amount'),
                'user_share' => $user_share,
                'municipality_share' => $municipality_share
            ];

        } else {
            return sendJson('error','خطا در احراز');
        }
    }

    public function insertPaymentServiceToUser()
    {
        $cashouts = Cashout::where('fava_id',null)->where('bank','SB24')->whereNotIn('id',[43354,43684, 51407, 67526,70328,71293,71767, 73756, 74618,])->whereDate('created_at','>=','2024-05-21')->where('status', 'deposited')->whereNotIn('user_id',[65332,64602,1,2,3]);
        if(auth()->id() == developerId()) {
            dump($cashouts->count());
        }
        $cashouts = $cashouts->take(30)->get();
        if($cashouts){
            foreach ($cashouts as $cashout) {
                $paymentId = 'saman_'.$cashout->id;
                $fava_id = Fava::GetPaymentIDService($paymentId);
                if($fava_id == -1){
                    $paymentDate = Carbon::instance($cashout->created_at)->format('c');
                    $fava_id = Fava::InsertPaymentServiceToUser($cashout->user,$cashout->amount,$paymentId,$paymentDate,$cashout->bank_id);
                }
                DB::table('cashouts')->where('id', $cashout->id)->update(['fava_id' => $fava_id]);
//                $cashout->fava_id = $fava_id;
//                $cashout->save();
                echo $fava_id . ' - ' . $cashout->id . '<br>';
            }
        }

//        if(auth()->id() == developerId() && $cashouts->count() > 0) {
//            sleep(3);
//            return redirect('/fava/userPayment');
//        }
    }
    public function insertMissPaymentServiceToUser()
    {
        $ids=[13370,13891,13397,14282,14370,14427,14757,14972,15376,15771,15894,17261,17441,17837,17915,18439,19412,19734,19790,19426, 20602,20608,20902,21664,21757,21786,
            22317,22684,22964,22991,24312,24411,24472,25068,25178,25297,29308,28492,28072,27377,27346,27329,27326,27303,27283,27279];
        $cashouts = Cashout::query()->whereNull('fava_id')
//            ->whereBetween(DB::raw('DATE(created_at)'), ['2023-03-21', '2024-05-21'])
            ->whereDate('created_at','<', '2023-05-21')
            ->where('status', 'deposited')
            ->whereNotIn('user_id', [65332, 64602, 1, 2, 3])
            ->whereNotIn('id',$ids)
            ->where('amount', '>=', 1000);
        if(auth()->id() == developerId()) {
            dump($cashouts->count());
        }
        $cashouts = $cashouts->take(30)->get();
        if($cashouts){
            foreach ($cashouts as $cashout) {
                $paymentId = 'bank_miss_'.$cashout->id;
                $fava_id = Fava::GetPaymentIDService($paymentId);
                if($fava_id == -1){
                    $paymentDate = Carbon::instance($cashout->created_at)->format('c');
                    if (isset($cashout->bank_id))
                    {
                        $bank_id = $cashout->bank_id;
                    }
                    else{
                        $bank_id= strval($cashout->trace_code).'-cashId-'.strval($cashout->id);
                    }
                    dump($bank_id);
                    $fava_id = Fava::InsertPaymentServiceToUser($cashout->user,$cashout->amount,$paymentId,$paymentDate,$bank_id);
                }
                DB::table('cashouts')->where('id', $cashout->id)->update(['fava_id' => $fava_id]);
              //  $cashout->fava_id = $fava_id;
              //  $cashout->save();
                echo $fava_id . ' - ' . $cashout->id . '<br>';
            }
        }

//        if(auth()->id() == developerId() && $cashouts->count() > 0) {
//            sleep(3);
//            return redirect('/fava/userPayment');
//        }
    }
    public function insertAsanPardakhtServiceToUser()
    {
        $cashouts = AsanPardakht::where('method', 'برداشت')
            ->whereNull('fava_id')
            ->whereNotNull('rrn')
            ->whereIn('type',['to_aap','submit_user'])
            ->whereNotIn('user_id', [65332, 64602, 1, 2, 3])
            ->where('city_id', 1);
        if(auth()->id() == developerId()) {
            dump($cashouts->count());
        }
        $cashouts = $cashouts->take(30)->get();
        if($cashouts){
            foreach ($cashouts as $cashout) {
                $paymentId = 'asan_'.$cashout->id;
                $fava_id = Fava::GetPaymentIDService($paymentId);
                if($fava_id == -1){
                    $paymentDate = Carbon::instance($cashout->created_at)->format('c');
                    $fava_id = Fava::InsertPaymentServiceToUser($cashout->user,$cashout->amount/10,$paymentId,$paymentDate,$cashout->rrn);
                }
                DB::table('asan_pardakhts')->where('id', $cashout->id)->update(['fava_id' => $fava_id]);
                echo $fava_id . ' - ' . $cashout->id . '<br>';
            }
        }

//        if(auth()->id() == developerId() && $cashouts->count() > 0) {
//            sleep(3);
//            return redirect('/fava/userPayment');
//        }
    }
    public function insertInaxServiceToUser()
    {
        $cashouts = Inax::query()
            ->where('pay_method', 'bazist')
            ->where('status', 'done')
            ->whereNotNull('ref_code')
            ->whereNull('fava_id')
            ->whereNotIn('user_id', [65332, 64602, 1, 2, 3]);
        if(auth()->id() == developerId()) {
            dump($cashouts->count());
        }
        $cashouts = $cashouts->take(30)->get();
        if($cashouts){
            foreach ($cashouts as $cashout) {
                $paymentId = 'inax_'.$cashout->id;
                $fava_id = Fava::GetPaymentIDService($paymentId);
                if($fava_id == -1){
                    $paymentDate = Carbon::instance($cashout->created_at)->format('c');
                    $fava_id = Fava::InsertPaymentServiceToUser($cashout->user,$cashout->amount,$paymentId,$paymentDate,$cashout->ref_code);
                }
                DB::table('inaxes')->where('id', $cashout->id)->update(['fava_id' => $fava_id]);
                echo $fava_id . ' - ' . $cashout->id . '<br>';
            }
        }

//        if(auth()->id() == developerId() && $cashouts->count() > 0) {
//            sleep(3);
//            return redirect('/fava/userPayment');
//        }
    }
}
