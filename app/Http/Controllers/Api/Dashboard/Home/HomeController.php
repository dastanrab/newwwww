<?php

namespace App\Http\Controllers\Api\Dashboard\Home;

use App\Http\Controllers\Controller;
use App\Models\AsanPardakht;
use App\Models\Cashout;
use App\Models\Contact;
use App\Models\Inax;
use App\Models\Submit;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public $jNow;
    public $diskFreeSpace;
    public $diskTotalSpace;
    public $asanpardakht;
    public $bazistWallet;
    public $inaxBalance;
    public $kavehnegarBalance;
    public $warehouseAzadiToday;
    public $warehouseMayameyToday;
    public $ticketCount;
    public $userCount;
    public $driverCount;
    public $submitCount;
    public $submitDoneCount;
    public $cardToCard;
    public $chartUserCount;
    public $weights;
    public $weightsLegal;
    public $weightsNotLegal;
    private  $comments;

    public function index()
    {
        $this->jNow = verta()->format('d F Y');
        $this->diskFreeSpace = round(disk_free_space('/') / 1000000000, 2);
        $this->diskTotalSpace = round(disk_total_space('/') / 1000000000, 2);

        $asanpardakhtOut = AsanPardakht::whereDate('created_at', '>=', '2023-10-09')->Where('method', 'برداشت')->pluck('amount')->sum();
        $asanpardakhtIn = AsanPardakht::whereDate('created_at', '>=', '2023-10-09')->Where('method', 'واریز')->pluck('amount')->sum();
        $this->asanpardakht = ($asanpardakhtIn-$asanpardakhtOut)/10;

        $wallets_sum = Wallet::where('wallet', '!=', 0)->pluck('wallet')->sum();
        $cashout_sum = Cashout::whereIn('status', ['waiting', 'depositing'])->pluck('amount')->sum();
        $this->bazistWallet = $wallets_sum+$cashout_sum;

//        $this->inaxBalance = \Illuminate\Support\Facades\Cache::get('inax_balance');
        $this->inaxBalance = Inax::balance();
        if (\Illuminate\Support\Facades\Cache::has('kave_remain_balance'))
        {
            $this->kavehnegarBalance=\Illuminate\Support\Facades\Cache::get('kave_remain_balance');
        }else{
            try {
                $response = Http::withOptions(['timeout' => 5])->get('https://api.kavenegar.com/v1/' .env('KAVENEGAR_API_KEY') . '/account/info.json');
                if ($response->ok()) {
                    $this->kavehnegarBalance= $response->json()['entries']['remaincredit'] / 10;
                } else {
                    $this->kavehnegarBalance= 0;
                }
                \Illuminate\Support\Facades\Cache::set('kave_remain_balance',$this->kavehnegarBalance,100);
            }catch (\Exception $exception)
            {
                $this->kavehnegarBalance= 0;
            }
        }

        $azadiIds = implode("','",User::azadiId());
        $mayameyIds = implode("','",User::mayameyId());
        $now = today();
        $this->warehouseAzadiToday = DB::select("SELECT sum(weights) as weight FROM drivers WHERE user_id IN(SELECT user_id FROM warehouse_dailies WHERE operator_id IN ('$azadiIds') AND DATE(created_at) = '$now') AND DATE(collected_at) = '$now'")[0]->weight;
        $this->warehouseMayameyToday = DB::select("SELECT sum(weights) as weight FROM drivers WHERE user_id IN(SELECT user_id FROM warehouse_dailies WHERE operator_id IN ('$mayameyIds') AND DATE(created_at) = '$now') AND DATE(collected_at) = '$now'")[0]->weight;

        $this->ticketCount = Contact::whereNull('admin_seen_at')->count();

        $this->userCount = User::count();

        $this->driverCount = User::with('roles','cars')->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })->whereHas('cars', function ($query)  {
            $query->where('is_active', 1);
        })->whereHas('cars', function ($query) {
            $query->where('rollcall_status', 2);
        })->count();

        $this->submitCount = Submit::whereNotIn('status',[4,5])->whereDate('start_deadline',today())->count();
        $this->submitDoneCount = Submit::where('status',3)->whereDate('start_deadline',today())->count();

        $this->cardToCard = Cashout::where('status','deposited')->whereDate('updated_at',today())->sum('amount');


        $dates = [];
        for ($i = 0; $i < 12; $i++) {
            $start = verta()->subMonths($i)->startMonth()->toCarbon()->format('Y-m-d H:i:s');
            $end = verta()->subMonths($i)->endMonth()->toCarbon()->format('Y-m-d H:i:s');
            $dates[] = [
                'start' => $start,
                'end' => $end,
                'label' => verta()->subMonths($i)->format('Y-m') // برچسب تاریخ شمسی
            ];
        }
        $case_sql = '';
        foreach ($dates as $date) {
            $case_sql .= "
        SUM(CASE WHEN created_at BETWEEN '{$date['start']}' AND '{$date['end']}' AND legal = 1 THEN 1 ELSE 0 END) AS `{$date['label']}_legal`,
        SUM(CASE WHEN created_at BETWEEN '{$date['start']}' AND '{$date['end']}' AND legal = 0 THEN 1 ELSE 0 END) AS `{$date['label']}_non_legal`,";
        }
        $case_sql = rtrim($case_sql, ', ');
        $raw_results = DB::select("SELECT $case_sql FROM users");
        $formatted_results = [];
        foreach ($dates as $date) {
            $formatted_results[] = [
                'date' => $date['label'],
                'legal' => $raw_results[0]->{"{$date['label']}_legal"},
                'non_legal' => $raw_results[0]->{"{$date['label']}_non_legal"}
            ];
        }
        $this->chartUserCount = collect($formatted_results);

        $weights=DB::table('drivers')
            ->join('submits', 'drivers.submit_id', '=', 'submits.id')
            ->join('users', 'users.id', '=', 'submits.user_id')
            ->select('users.legal', DB::raw('SUM(drivers.weights) as weight'))
            ->where('drivers.status', 3)
            ->whereDate('submits.start_deadline', today())
            ->groupBy('users.legal')
            ->get();
        $this->weightsLegal = 0;
        $this->weightsNotLegal = 0;
        foreach ($weights as $weight) {
            if ($weight->legal == 1) {
                $this->weightsLegal=$weight->weight;
            }
            else{
                $this->weightsNotLegal=$weight->weight;
            }
        }
        $this->weights = $this->weightsLegal+$this->weightsNotLegal;
        $this->comments= Submit::query()->select(['id','user_id','star','comment','created_at'])->with(['user','drivers.user'])->whereNotNull('comment')->where('survey',1)->orderBy('id','DESC')->limit(3)->get();
        return success_response(['data']);
    }
}
