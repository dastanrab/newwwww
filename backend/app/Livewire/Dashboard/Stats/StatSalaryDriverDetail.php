<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\BazistWallet;
use App\Models\Car;
use App\Models\DriversAttendanceLogs;
use App\Models\DriversSalaryDetails;
use App\Models\DriversSalaryPay;
use App\Models\User;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StatSalaryDriverDetail extends Component
{
    use WithPagination;
    public $breadCrumb = [['حقوق رانندگان','d.stats.salary-driver'], ['جزییات حقوق راننده','d.stats.salary-driver-detail']];
    #[Title('حقوق رانندگان > جزییات حقوق راننده')]
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    public User $driver;

    public function render()
    {
//        $this->authorize('user_driver_single',User::class);
        return view('livewire.dashboard.stats.stat-salary-driver-detail');
    }

    #[Computed]
    public function wallet_deposite()
    {
        $result = BazistWallet::query()->select('user_id', DB::raw('DATE(created_at) as formated_date,SUM(amount) as amount'))->where('user_id',$this->user_ids)
            ->where('method','واریز');
        if(isset($this->dateFrom) and isset($this->dateTo)){
            $dateFrom = Verta::parse($this->dateFrom)->toCarbon()->format('Y-m-d 00:00:00');
            $dateTo = Verta::parse($this->dateTo)->toCarbon()->format('Y-m-d 23:59:59');
            $result=$result->whereBetween('formated_date',[$dateFrom,$dateTo]);
        }
        $result=$result->groupBy('formated_date')->get();
        return $result->keyBy('formated_date')->toArray();
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
        $result=$result->groupBy('user_id')->get();
       return $result->keyBy('user_id')->toArray();

    }
    #[Computed]
    public function detail()
    {
        $result = DriversSalaryDetails::query()->select(['id','submit_id','user_id','distance','total_attendance','weight','metals_reward','weight_price','reward_price',DB::raw('date(created_at) as created_at')])
        ->where('user_id',$this->driver->id);
        if(isset($this->dateFrom)){
            $dateFrom = Verta::parse($this->dateFrom)->toCarbon()->format('Y-m-d 00:00:00');
            $result=$result->whereDate('created_at',$dateFrom);
        }
        return $result->paginate(50);

    }

    #[On('dateFrom')]
    public function dateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }
}
