<?php

namespace App\Http\Controllers;

use App\Classes\BaleService;
use App\Events\ActivityEvent;
use App\Jobs\CalculateDriverDistance;
use App\Models\ArchiveLegal;
use App\Models\ArchivePhone;
use App\Models\AsanPardakht;
use App\Models\BazistWallet;
use App\Models\Car;
use App\Models\Cashout;
use App\Models\Driver;
use App\Models\Inax;
use App\Models\Receive;
use App\Models\ReceiveArchive;
use App\Models\Rollcall;
use App\Models\Submit;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Kavenegar\KavenegarApi;

class JobController extends Controller
{
    public function rollCallReset()
    {
        $now = now()->format('H:i:s');
        if($now > '01:00:00' && $now < '07:00:00'){
            $myfile = fopen("/home/laravel/la.bazistco.com/storage/logs/cronjob.txt", "a+") or die("Unable to open file!");
            $txt = '1:'.date('Y-m-d H:i:s')."\n";
            fwrite($myfile, $txt);
            Car::where('is_active',1)->update(['rollcall_status' => 0]);
        }
    }
    public function DriversDistance(Request $request)
    {
        $drivers=  User::whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })->whereIn('id',test_drivers())->get();
        foreach ($drivers as $driver) {
            if ($request->get('date') !== null)
            {
                $date=$request->get('date');
            }
            else{
                $date=now();
            }
            CalculateDriverDistance::dispatch($driver,$date)->onConnection('redis-queue')->onQueue('redis-queue');
        }
        return response()->json(['status' => 'Jobs dispatched!']);
    }

    public function statArchive(Request $request)
    {

        if($request->date){
            $date = verta()->parse($request->date)->toCarbon()->format('Y-m-d');
        }
        else{
            $date = now()->format('Y-m-d');
        }
        $city_id = 1;
        $archive = ReceiveArchive::whereDate('date',$date)->where('city_id',$city_id)->first();

        $this->calculateArchiveAll($archive,$date,$city_id);
        $this->calculateArchivePhone($archive,$date,$city_id);
        $this->calculateArchiveLegal($archive,$date,$city_id);
        $this->calculateArchiveNotLegal($archive,$date,$city_id);
        $this->calculateArchiveApp($archive,$date,$city_id);

        if(isset($request->date)) {
            $dateExp = explode('-', $request->date);
            if (auth()->id() == developerId() && $dateExp[2] > 1) {
                sleep(3);
                return redirect('/stats/archive?date=' . $dateExp[0] . '-' . $dateExp[1] . '-' . $dateExp[2] - 1);
            }
        }

    }

    public function financialArchive(Request $request)
    {
        try {
            $date=\Carbon\Carbon::now()->subDay()->format('Y-m-d');
            $total_depositing=DB::table('cashouts')
                ->where(function ($query) {
                    $query->whereIn('status', ['waiting', 'depositing']);
                })
                ->whereDate('updated_at', '<', $date)
                ->sum(DB::raw('amount * 10'));
            $insert=today_fin($date);
            if (isset($insert['cashout_waiting']))
            {
                $insert['cashout_today_waiting']=$insert['cashout_waiting'];
                $insert['cashout_waiting']=$insert['cashout_waiting']+$total_depositing;
            }
            else{
                $insert['cashout_today_waiting']=0;
                $insert['cashout_waiting']=$total_depositing;
            }
            $aap_total_deposit = AsanPardakht::whereDate('created_at', '<=', $date)->whereDate('created_at', '>=', '2023-10-09')->where('method', 'واریز')->sum('amount');
            $aap_total_withdraw = AsanPardakht::whereDate('created_at', '<=', $date)->whereDate('created_at', '>=', '2023-10-09')->where('method', 'برداشت')->sum('amount');
            $app_total = ($aap_total_deposit - $aap_total_withdraw);
            $aap_deposit = AsanPardakht::whereDate('created_at', $date)->where('method', 'واریز')->sum('amount');
            $app_withdraw = AsanPardakht::whereDate('created_at', $date)->where('method', 'برداشت')->sum('amount');
            $rewards_first_bazist = BazistWallet::whereDate('created_at', $date)->where('type','first_submit_user')->sum('amount');
            $rewards_first_asan = AsanPardakht::whereDate('created_at', $date)->where('type','first_submit_user')->sum('amount');
            $rewards_first = $rewards_first_bazist+$rewards_first_asan;
            $rewards_first = $rewards_first > 0 ? $rewards_first : $rewards_first;
            $rewards_ref_bazist = BazistWallet::whereDate('created_at', $date)->where('details','LIKE','%پاداش معرف%')->whereIn('type',['submit_user_ref','deposit'])->sum('amount');
            $rewards_ref_asan = AsanPardakht::whereDate('created_at', $date)->where('type','submit_user_ref')->sum('amount');
            $rewards_ref = $rewards_ref_bazist+$rewards_ref_asan;
            $rewards_ref = $rewards_ref > 0 ? $rewards_ref : $rewards_ref;
            $wallets_sum = Wallet::where('wallet', '!=', 0)->pluck('wallet')->sum()*10;
            $cashout_sum = Cashout::whereIn('status', ['waiting', 'depositing'])->pluck('amount')->sum()*10;
            $bazist_wallet = $wallets_sum+$cashout_sum;
            $cashout = Cashout::whereDate('created_at', $date)->where('trace_code', '!=', null)->sum('amount')*10;
            $office_deposit = BazistWallet::whereDate('created_at', $date)->whereIn('user_id',[65332,64602])->where('method','واریز')->sum('amount');
            $office_withdraw = BazistWallet::whereDate('created_at', $date)->whereIn('user_id',[65332,64602])->where('method','برداشت')->sum('amount');
            $insert['aap_amount']=@$app_total;
            $insert['aap_deposite']=$aap_deposit;
            $insert['aap_withdraw']=$app_withdraw;
            $insert['raw_bazist_wallet_amount']=$wallets_sum;
            $insert['bazist_wallet_amount']=$bazist_wallet;
            $insert['cashout']=$cashout;
            $insert['deposite']=$office_deposit;
            $insert['withdraw']=$office_withdraw;
            $insert['first_submit_amount']=$rewards_first;
            $insert['ref_amount']=$rewards_ref;
            $insert['bazist_total_bardaasht_amount']=@get_bazist_total_bardaasht_amount($date)*10;
            $insert['bazist_total_vaariz_amount']=@get_bazist_total_vaariz_amount($date)*10;
            $insert['waste_amount']=@get_waste_amount($date)*10;
            $insert['created_at']=$date;
            $insert['updated_at']=$date;
            if (DB::table('financial_archive')->whereDate('created_at', '=', $date)->exists()) {
                return 'exist!';
            }else{
                DB::table('financial_archive')->insert($insert);
            }
        }catch (\Exception $e){
            app(BaleService::class)->HotLog($e);
        }
    }

    public function calculateArchiveAll($archive,$date,$city_id)
    {
        $submits = Submit::whereDate('start_deadline', $date)->where('city_id', $city_id)->get();
        $legal_collects_counts=DB::select("SELECT u.legal ,count(*) as count  from submits as s join users as u on s.user_id = u.id WHERE   s.`status` = 3 AND DATE(s.start_deadline) = '$date' GROUP BY u.legal");
        foreach ($legal_collects_counts as $legal_collects_count )
        {
            if ($legal_collects_count->legal == 1)
            {
                $legalCollected = $legal_collects_count->count;
            }
            else{
                $notLegalCollected = $legal_collects_count->count;
            }
        }
        $drivers = Driver::whereDate('collected_at', $date)->where('status',3)->where('city_id', $city_id)->with(['submit', 'receives'])->get();
        $submitCount = $submits->count();
        $submitDone = $submits->where('status',3)->count();
        $submitFirst = 0;
        $submitCancel = $submits->where('status', 4)->count();
        $submitDelete = $submits->where('status', 5)->count();
        $weight = round($drivers->pluck('weights')->sum(), 2);
        $value = $submits->sum('final_amount');
        $userPay = $submits->sum('total_amount');
        $favaPay = $value*0.05;
        $favaPayShare = 0;
        //$recyclable_1 = $drivers->receives()->where('fava_id',1)->sum('weight'); //2475.480
        $recyclable = [];
        for($i=1;$i<=22;$i++){
            $recyclable[$i] = Receive::whereDate('created_at', $date)->where('fava_id', $i)->pluck('weight')->sum();
        }

        $data = [
            'city_id'        => $city_id,
            'date'           => $date,
            'type'           => 1,
            'submit_count'   => $submitCount,
            'submit_done'    => $submitDone,
            'submit_first'   => $submitFirst,
            'submit_cancel'  => $submitCancel,
            'submit_delete'  => $submitDelete,
            'weight'         => $weight,
            'value'          => $value*10,
            'user_pay'       => $userPay*10,
            'fava_pay'       => $favaPay*10,
            'fava_pay_share' => $favaPayShare*10,
            'recyclable_1'   => $recyclable[1],
            'recyclable_2'   => $recyclable[2],
            'recyclable_3'   => $recyclable[3],
            'recyclable_4'   => $recyclable[4],
            'recyclable_5'   => $recyclable[5],
            'recyclable_6'   => $recyclable[6],
            'recyclable_7'   => $recyclable[7],
            'recyclable_8'   => $recyclable[8],
            'recyclable_9'   => $recyclable[9],
            'recyclable_10'  => $recyclable[10],
            'recyclable_11'  => $recyclable[11],
            'recyclable_12'  => $recyclable[12],
            'recyclable_13'  => $recyclable[13],
            'recyclable_14'  => $recyclable[14],
            'recyclable_15'  => $recyclable[15],
            'recyclable_16'  => $recyclable[16],
            'recyclable_17'  => $recyclable[17],
            'recyclable_18'  => $recyclable[18],
            'recyclable_19'  => $recyclable[19],
            'recyclable_20'  => $recyclable[20],
            'recyclable_21'  => $recyclable[21],
            'recyclable_22'  => $recyclable[22],
            'legal_submit_count'=>$legalCollected??0,
            'illegal_submit_count'=>$notLegalCollected??0
        ];
        dump($data);
        if($archive){
            $archive->update($data);
        }
        else{
            $archive = ReceiveArchive::create($data);
        }
        return $archive;
    }

    public function calculateArchivePhone($archive,$date,$city_id)
    {
        /*****************************  Archive Phone ***********************************/
        $submitsPhone = Submit::whereDate('start_deadline', $date)->where('city_id', $city_id)->where('submit_phone', 1)->get();
        $driversPhone = Driver::whereDate('collected_at', $date)->where('status',3)->where('city_id', $city_id)->whereHas('submit', function ($q){
            $q->where('submit_phone',1);
        })->with(['submit', 'receives'])->get();
        $submitPhoneCount = $submitsPhone->count();
        $submitPhoneDone = $submitsPhone->where('status',3)->count();
        $submitPhoneFirst = 0;
        $submitPhoneCancel = $submitsPhone->where('status', 4)->count();
        $submitPhoneDelete = $submitsPhone->where('status', 5)->count();
        $weightPhone = round($driversPhone->pluck('weights')->sum(), 2);
        $valuePhone = $submitsPhone->sum('final_amount');
        $userPayPhone = $submitsPhone->sum('total_amount');
        $favaPayPhone = $valuePhone*0.05;
        $favaPaySharePhone = 0;
        $recyclablePhone = [];
        for($i=1;$i<=22;$i++){
            $recyclablePhone[$i] = DB::table('receives')->join('drivers', 'receives.driver_id', '=', 'drivers.id')->join('submits', 'submits.id', '=', 'drivers.submit_id')->where('submits.submit_phone',1)->whereDate('receives.created_at', $date)->where('receives.fava_id', $i)->pluck('receives.weight')->sum();
        }
        $data = [
            'city_id'        => $city_id,
            'date'           => $date,
            'type'           => 1,
            'submit_count'   => $submitPhoneCount,
            'submit_done'    => $submitPhoneDone,
            'submit_first'   => $submitPhoneFirst,
            'submit_cancel'  => $submitPhoneCancel,
            'submit_delete'  => $submitPhoneDelete,
            'weight'         => $weightPhone,
            'value'          => $valuePhone*10,
            'user_pay'       => $userPayPhone*10,
            'fava_pay'       => $favaPayPhone*10,
            'fava_pay_share' => $favaPaySharePhone*10,
            'recyclable_1'   => $recyclablePhone[1],
            'recyclable_2'   => $recyclablePhone[2],
            'recyclable_3'   => $recyclablePhone[3],
            'recyclable_4'   => $recyclablePhone[4],
            'recyclable_5'   => $recyclablePhone[5],
            'recyclable_6'   => $recyclablePhone[6],
            'recyclable_7'   => $recyclablePhone[7],
            'recyclable_8'   => $recyclablePhone[8],
            'recyclable_9'   => $recyclablePhone[9],
            'recyclable_10'  => $recyclablePhone[10],
            'recyclable_11'  => $recyclablePhone[11],
            'recyclable_12'  => $recyclablePhone[12],
            'recyclable_13'  => $recyclablePhone[13],
            'recyclable_14'  => $recyclablePhone[14],
            'recyclable_15'  => $recyclablePhone[15],
            'recyclable_16'  => $recyclablePhone[16],
            'recyclable_17'  => $recyclablePhone[17],
            'recyclable_18'  => $recyclablePhone[18],
            'recyclable_19'  => $recyclablePhone[19],
            'recyclable_20'  => $recyclablePhone[20],
            'recyclable_21'  => $recyclablePhone[21],
            'recyclable_22'  => $recyclablePhone[22],
        ];
        dump($data);
        if($archive->archivePhone){
            $archive->archivePhone->update($data);
        }
        else{
            $archive->archivePhone()->create($data);
        }
    }

    public function calculateArchiveLegal($archive,$date,$city_id)
    {
        /***************************** Archive Legal ***********************************/
        $submitsLegal = Submit::whereDate('start_deadline', $date)->where('city_id', $city_id)->whereHas('user', function ($q){
            $q->where('legal',1);
        })->get();

        $driversLegal = DB::table('drivers')
            ->join('submits', 'drivers.submit_id', '=', 'submits.id')
            ->join('users', 'users.id', '=', 'submits.user_id')
            ->whereDate('drivers.collected_at', $date)
            ->where('users.legal', 1)->get();

        $submitLegalCount = $submitsLegal->count();
        $submitLegalDone = $submitsLegal->where('status',3)->count();
        $submitLegalFirst = 0;
        $submitLegalCancel = $submitsLegal->where('status', 4)->count();
        $submitLegalDelete = $submitsLegal->where('status', 5)->count();
        $weightLegal = round($driversLegal->pluck('weights')->sum(), 2);
        $valueLegal = $submitsLegal->sum('final_amount');
        $userPayLegal = $submitsLegal->sum('total_amount');
        $favaPayLegal = $valueLegal*0.05;
        $favaPayShareLegal = 0;
        $recyclableLegal = [];
        for($i=1;$i<=22;$i++){
            $recyclableLegal[$i] = DB::table('receives')
                ->join('drivers', 'receives.driver_id', '=', 'drivers.id')
                ->join('submits', 'submits.id', '=', 'drivers.submit_id')
                ->join('users', 'users.id', '=', 'submits.user_id')
                ->where('users.legal',1)
                ->whereDate('receives.created_at', $date)
                ->where('receives.fava_id', $i)
                ->pluck('receives.weight')->sum();
        }
        $data = [
            'city_id'        => $city_id,
            'date'           => $date,
            'type'           => 1,
            'submit_count'   => $submitLegalCount,
            'submit_done'    => $submitLegalDone,
            'submit_first'   => $submitLegalFirst,
            'submit_cancel'  => $submitLegalCancel,
            'submit_delete'  => $submitLegalDelete,
            'weight'         => $weightLegal,
            'value'          => $valueLegal*10,
            'user_pay'       => $userPayLegal*10,
            'fava_pay'       => $favaPayLegal*10,
            'fava_pay_share' => $favaPayShareLegal*10,
            'recyclable_1'   => $recyclableLegal[1],
            'recyclable_2'   => $recyclableLegal[2],
            'recyclable_3'   => $recyclableLegal[3],
            'recyclable_4'   => $recyclableLegal[4],
            'recyclable_5'   => $recyclableLegal[5],
            'recyclable_6'   => $recyclableLegal[6],
            'recyclable_7'   => $recyclableLegal[7],
            'recyclable_8'   => $recyclableLegal[8],
            'recyclable_9'   => $recyclableLegal[9],
            'recyclable_10'  => $recyclableLegal[10],
            'recyclable_11'  => $recyclableLegal[11],
            'recyclable_12'  => $recyclableLegal[12],
            'recyclable_13'  => $recyclableLegal[13],
            'recyclable_14'  => $recyclableLegal[14],
            'recyclable_15'  => $recyclableLegal[15],
            'recyclable_16'  => $recyclableLegal[16],
            'recyclable_17'  => $recyclableLegal[17],
            'recyclable_18'  => $recyclableLegal[18],
            'recyclable_19'  => $recyclableLegal[19],
            'recyclable_20'  => $recyclableLegal[20],
            'recyclable_21'  => $recyclableLegal[21],
            'recyclable_22'  => $recyclableLegal[22],
        ];
        dump($data);
        if($archive->archiveLegal){
            $archive->archiveLegal->update($data);
        }
        else{
            $archive->archiveLegal()->create($data);
        }
    }

    public function calculateArchiveNotLegal($archive,$date,$city_id)
    {
        $archiveLegal = ArchiveLegal::whereDate('date',$date)->first();
        $data = [
            'city_id'        => $city_id,
            'date'           => $date,
            'type'           => 1,
            'submit_count'   => $archive->submit_count-$archiveLegal->submit_count,
            'submit_done'    => $archive->submit_done-$archiveLegal->submit_done,
            'submit_first'   => $archive->submit_first-$archiveLegal->submit_first,
            'submit_cancel'  => $archive->submit_cancel-$archiveLegal->submit_cancel,
            'submit_delete'  => $archive->submit_delete-$archiveLegal->submit_delete,
            'weight'         => $archive->weight-$archiveLegal->weight,
            'value'          => ($archive->value-$archiveLegal->value)*10,
            'user_pay'       => ($archive->user_pay-$archiveLegal->user_pay)*10,
            'fava_pay'       => ($archive->fava_pay-$archiveLegal->fava_pay)*10,
            'fava_pay_share' => ($archive->fava_pay_share-$archiveLegal->fava_pay_share)*10,
            'recyclable_1'   => $archive->recyclable_1-$archiveLegal->recyclable_1,
            'recyclable_2'   => $archive->recyclable_2-$archiveLegal->recyclable_2,
            'recyclable_3'   => $archive->recyclable_3-$archiveLegal->recyclable_3,
            'recyclable_4'   => $archive->recyclable_4-$archiveLegal->recyclable_4,
            'recyclable_5'   => $archive->recyclable_5-$archiveLegal->recyclable_5,
            'recyclable_6'   => $archive->recyclable_6-$archiveLegal->recyclable_6,
            'recyclable_7'   => $archive->recyclable_7-$archiveLegal->recyclable_7,
            'recyclable_8'   => $archive->recyclable_8-$archiveLegal->recyclable_8,
            'recyclable_9'   => $archive->recyclable_9-$archiveLegal->recyclable_9,
            'recyclable_10'  => $archive->recyclable_10-$archiveLegal->recyclable_10,
            'recyclable_11'  => $archive->recyclable_11-$archiveLegal->recyclable_11,
            'recyclable_12'  => $archive->recyclable_12-$archiveLegal->recyclable_12,
            'recyclable_13'  => $archive->recyclable_13-$archiveLegal->recyclable_13,
            'recyclable_14'  => $archive->recyclable_14-$archiveLegal->recyclable_14,
            'recyclable_15'  => $archive->recyclable_15-$archiveLegal->recyclable_15,
            'recyclable_16'  => $archive->recyclable_16-$archiveLegal->recyclable_16,
            'recyclable_17'  => $archive->recyclable_17-$archiveLegal->recyclable_17,
            'recyclable_18'  => $archive->recyclable_18-$archiveLegal->recyclable_18,
            'recyclable_19'  => $archive->recyclable_19-$archiveLegal->recyclable_19,
            'recyclable_20'  => $archive->recyclable_20-$archiveLegal->recyclable_20,
            'recyclable_21'  => $archive->recyclable_21-$archiveLegal->recyclable_21,
            'recyclable_22'  => $archive->recyclable_22-$archiveLegal->recyclable_22,
        ];
        dump($data);
        if($archive->archiveNotLegal){
            $archive->archiveNotLegal->update($data);
        }
        else{
            $archive->archiveNotLegal()->create($data);
        }

    }

    public function calculateArchiveApp($archive,$date,$city_id)
    {
        $archivePhone = ArchivePhone::whereDate('date',$date)->first();
        $data = [
            'city_id'        => $city_id,
            'date'           => $date,
            'type'           => 1,
            'submit_count'   => $archive->submit_count-$archivePhone->submit_count,
            'submit_done'    => $archive->submit_done-$archivePhone->submit_done,
            'submit_first'   => $archive->submit_first-$archivePhone->submit_first,
            'submit_cancel'  => $archive->submit_cancel-$archivePhone->submit_cancel,
            'submit_delete'  => $archive->submit_delete-$archivePhone->submit_delete,
            'weight'         => $archive->weight-$archivePhone->weight,
            'value'          => ($archive->value-$archivePhone->value)*10,
            'user_pay'       => ($archive->user_pay-$archivePhone->user_pay)*10,
            'fava_pay'       => ($archive->fava_pay-$archivePhone->fava_pay)*10,
            'fava_pay_share' => ($archive->fava_pay_share-$archivePhone->fava_pay_share)*10,
            'recyclable_1'   => $archive->recyclable_1-$archivePhone->recyclable_1,
            'recyclable_2'   => $archive->recyclable_2-$archivePhone->recyclable_2,
            'recyclable_3'   => $archive->recyclable_3-$archivePhone->recyclable_3,
            'recyclable_4'   => $archive->recyclable_4-$archivePhone->recyclable_4,
            'recyclable_5'   => $archive->recyclable_5-$archivePhone->recyclable_5,
            'recyclable_6'   => $archive->recyclable_6-$archivePhone->recyclable_6,
            'recyclable_7'   => $archive->recyclable_7-$archivePhone->recyclable_7,
            'recyclable_8'   => $archive->recyclable_8-$archivePhone->recyclable_8,
            'recyclable_9'   => $archive->recyclable_9-$archivePhone->recyclable_9,
            'recyclable_10'  => $archive->recyclable_10-$archivePhone->recyclable_10,
            'recyclable_11'  => $archive->recyclable_11-$archivePhone->recyclable_11,
            'recyclable_12'  => $archive->recyclable_12-$archivePhone->recyclable_12,
            'recyclable_13'  => $archive->recyclable_13-$archivePhone->recyclable_13,
            'recyclable_14'  => $archive->recyclable_14-$archivePhone->recyclable_14,
            'recyclable_15'  => $archive->recyclable_15-$archivePhone->recyclable_15,
            'recyclable_16'  => $archive->recyclable_16-$archivePhone->recyclable_16,
            'recyclable_17'  => $archive->recyclable_17-$archivePhone->recyclable_17,
            'recyclable_18'  => $archive->recyclable_18-$archivePhone->recyclable_18,
            'recyclable_19'  => $archive->recyclable_19-$archivePhone->recyclable_19,
            'recyclable_20'  => $archive->recyclable_20-$archivePhone->recyclable_20,
            'recyclable_21'  => $archive->recyclable_21-$archivePhone->recyclable_21,
            'recyclable_22'  => $archive->recyclable_22-$archivePhone->recyclable_22,
        ];
        dump($data);
        if($archive->archivePhone){
            $archive->archivePhone->update($data);
        }
        else{
            $archive->archivePhone()->create($data);
        }
    }
    public function updateArchive()
    {
        $start = Cache::get('start', '2024-03-20');
        $total=DB::select("SELECT
  u.legal,
  count( * ) AS count ,
  Date(s.start_deadline) as date
FROM
  submits AS s
  JOIN users AS u ON s.user_id = u.id
WHERE
  s.`status` = 3
  and
  date(s.start_deadline) > '{$start}'
GROUP BY
  u.legal,
  DATE(s.start_deadline)");
        $data=[];
        foreach ($total as $item)
        {
            if ($item->legal == 1)
            {
                $data[$item->date]['legal']=$item->count;
            }
            if ($item->legal == 0)
            {
                $data[$item->date]['illegal']=$item->count;
            }
        }
        if (count($data)>0) {
            $dates = collect($total)->pluck('date')->unique()->toArray();
            $recieve_archives = ReceiveArchive::whereIn(DB::raw("DATE(date)"), $dates)->get();
            foreach ($recieve_archives as $recieve_archive)
            {
                $date=Carbon::createFromFormat('Y-m-d H:i:s', $recieve_archive->date)->format('Y-m-d');
                $recieve_archive->update(['legal_submit_count'=>$data[$date]['legal'],'illegal_submit_count'=>$data[$date]['illegal']]);
                Cache::set('start',$date,120);
            }
        }
        return response()->json(['status'=>'done']);
    }

    public function inventories()
    {
        try {
            $response = Http::withOptions(['timeout' => 5])->get('https://api.kavenegar.com/v1/' .env('KAVENEGAR_API_KEY') . '/account/info.json');
            if ($response->ok()) {
                $kaveh =  $response->json()['entries']['remaincredit'] / 10;
            } else {
                $kaveh = 0;
            }
            Cache::put('kave_balance',$kaveh);
        } catch (\Exception $e) {
            event(new ActivityEvent($response->throw(), 'Kavenegar', false));
        }
        $inax = Inax::balance();
        Cache::put('inax_balance',$inax);
        return [$kaveh,$inax];
    }
    public function walletsArchive(Request $request)
    {
        $date=\Carbon\Carbon::now()->subDay()->format('Y-m-d');
        if (DB::table('wallet_logs')->whereDate('logged_at', '=', $date)->exists()) {
            return 'exist!';
        }else{
            $wallets = DB::table('wallets')->select(['user_id','wallet'])->whereNot('wallet',0)->pluck('wallet', 'user_id')->toArray();
            $wallet=\App\Models\WalletLog::query()->create(['data'=>$wallets,'logged_at'=>$date]);
//            $bale= new \App\Classes\BaleService();
//            $bale->WalletLog($wallet->id);
            return 'ok';
        }
    }
}
