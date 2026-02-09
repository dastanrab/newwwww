<?php

namespace App\Livewire\Dashboard\Stats;

use App\Classes\TransactionService;
use App\Models\BazistWallet;
use App\Models\DriversSalaryDetails;
use App\Models\DriversSalaryPay;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Exception;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StatSalaryDriverIndexList extends Component
{
    use WithPagination;
    #[Url]
    public $date;
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    private array $user_ids;
    private array $wallet_ids;

    public function render()
    {
        return view('livewire.dashboard.stats.stat-salary-driver-index-list');
    }
    #[Computed]
    public function drivers()
    {

        $query = User::with(['roles','cars','drivers.submit.address','drivers','wallet'])->whereIn('id',test_drivers())->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        });
        $query=$query->orderBy('created_at', 'desc')->paginate(15);
        foreach ($query->items() as $driver)
        {
            $this->user_ids[]=$driver->id;
            $this->wallet_ids[]=$driver->wallet->id;
        }
        return $query;
    }
    #[Computed]
    public function details()
    {
        $result = DriversSalaryDetails::query()->select([DB::raw('user_id,sum(distance) as distance,avg(total_attendance) as total_attendance,sum(metals_reward) as metal_reward,avg(weight_price) as weight_price,SUM(IF(salary_type = 0 , reward_price, -CAST(reward_price AS SIGNED)))  as reward_price , sum(weight) as weight')]);
        if(isset($this->dateFrom)){
            $dateFrom = Verta::parse($this->dateFrom)->toCarbon()->format('Y-m-d 00:00:00');
            $result=$result->whereDate('created_at',$dateFrom);
            $result=$result->groupBy('user_id')->get();
            $result=$result->keyBy('user_id')->toArray();
        }
        else{
            $today_result=[];
            $result=DB::table('drivers')->select([DB::raw('user_id , sum(weights) as weight')])
                ->whereDate('collected_at', \Carbon\Carbon::now()->format('Y-m-d'))->groupBy('user_id')
                ->get();
            $result=$result->keyBy('user_id')->toArray();
            foreach ($result as $key => $value) {
                $today_result[$key]=["user_id" => 8,
                    "distance" => 0,
                    "total_attendance" => 0.0,
                    "metal_reward" => 0.0,
                    "weight_price" => 0,
                    "reward_price" => 0,
                    "weight" => $value->weight??0];
            }
            $result=$today_result;
        }

        return $result;
    }
    #[Computed]
    public function wallet_deposite()
    {
        $result = DB::table('wallet_details')
            ->select('wallet_id', DB::raw("SUM(IF(method = 'واریز', amount, -CAST(amount AS SIGNED))) AS amount"))
            ->whereIn('wallet_id',$this->wallet_ids)
            ->groupBy('wallet_id');
        if(isset($this->dateFrom) and isset($this->dateTo)){
            $dateFrom = Verta::parse($this->dateFrom)->toCarbon()->format('Y-m-d 00:00:00');
            $dateTo = Verta::parse($this->dateTo)->toCarbon()->format('Y-m-d 23:59:59');
            $result=$result->whereBetween('created_at',[$dateFrom,$dateTo]);
        }else{
//            $startOfMonth = Carbon::now()->subDays(30);
//            $endOfMonth = Carbon::now()->endOfMonth();
//            $result=$result->whereBetween('created_at',[$startOfMonth,$endOfMonth]);
        }
        $result=$result->get();
        $result=$result->keyBy('wallet_id')->toArray();
        return $result;
    }
    #[Computed]
    public function salary_pays()
    {

        $result = DriversSalaryPay::query()->select('user_id', DB::raw('SUM(amount) as amount'))->whereIn('user_id',$this->user_ids);
        if(isset($this->dateFrom) and isset($this->dateTo)){
            $dateFrom = Verta::parse($this->dateFrom)->toCarbon()->format('Y-m-d 00:00:00');
            $dateTo = Verta::parse($this->dateTo)->toCarbon()->format('Y-m-d 23:59:59');
            $result=$result->whereBetween('created_at',[$dateFrom,$dateTo]);
        }
        else{
            $startOfMonth = Carbon::now()->subDays(30);
            $endOfMonth = Carbon::now()->endOfMonth();
            $result=$result->whereBetween('created_at',[$startOfMonth,$endOfMonth]);
        }
        $result=$result->groupBy('user_id')->get();
        $result=$result->keyBy('user_id')->toArray();
        return $result;
    }
    #[On('dateFrom')]
    public function dateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }
    #[On('dateTo')]
    public function dateTo($dateTo)
    {
        $this->dateTo = $dateTo;
    }
    #[On('pay')]
    public function pay(User $user,$amount)
    {
        $driver_salary=DriversSalaryDetails::query()->select([DB::raw('SUM(IF(salary_type = 0 , reward_price, -CAST(reward_price AS SIGNED))) as salary')])->where('user_id',$user->id)->first();
       $amount=(int)$amount;
        if ($amount > $driver_salary->salary) {
            sendToast(0, "مبلغ نامعتبر است ");
        }
        else
        {
            try {
                DB::beginTransaction();
                $description=' پرداخت پاداش راننده ';
                $user = User::find($user->id);
                $wallet = Wallet::where('user_id', $user->id)->first();
                $wallet->wallet = $wallet->wallet + $amount;
                $wallet->save();
                $salary_WITHDRAWAL= DriversSalaryDetails::query()->create(['user_id'=>$user->id,'reward_price'=>$amount,'salary_type'=>1,'creator_id'=>auth()->user()->id]);
                BazistWallet::create($user->city->id, $user->id, $wallet->id, 'driver_reward',  $salary_WITHDRAWAL->id, $amount * 10, $wallet->wallet * 10, 'واریز', $description);
                create_transaction(0,$user->id,$amount,TransactionService::BAZIST_TYPE,TransactionService::BAZIST_TYPE,TransactionService::WASTE_RREASON,$salary_WITHDRAWAL->id);
                DB::commit();
                sendToast(1, "مبلغ {$amount} تومان کیف پول آنیروب راننده واریز شد.");
            }
            catch (Exception $e){
                DB::rollBack();
                sendToast(0,'خطا در واریز حقوق');
            }
        }
    }

}
