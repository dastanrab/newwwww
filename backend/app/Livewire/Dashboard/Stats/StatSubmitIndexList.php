<?php

namespace App\Livewire\Dashboard\Stats;

use App\Events\ActivityEvent;
use App\Models\BazistWallet;
use App\Models\Car;
use App\Models\City;
use App\Models\Polygon;
use App\Models\Referrer;
use App\Models\Submit;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StatSubmitIndexList extends Component
{
    use WithPagination;
    public $is_analyst;
    public $row = 7;
    #[Url]
    public $id;
    #[Url]
    public $status;
    #[Url]
    public $driverId;
    #[Url]
    public $type;
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    #[Url]
    public $search;
    /**
     * @var array|\Illuminate\Foundation\Application|\Illuminate\Session\SessionManager|mixed|mixed[]|null
     */
    private  $city_id;

    public function boot()
    {
        $this->city_id = session('city',1) ;
        if ($this->city_id == 0){
            $this->city_id=City::all()->pluck('id')->toArray();
        }
        else{
            $this->city_id=[$this->city_id];
        }
    }
    public function render()
    {
        $this->is_analyst=auth()->user()->getRoles()->contains('analyst');
        return view('livewire.dashboard.stats.stat-submit-index-list');
    }



    #[Computed]
    public function submits()
    {
        $query = Submit::query();
        $city = $this->city_id;
        $query->when($city, function ($query, $city) {
            return $query->whereIn('city_id', $city);
        })
        ->orderBy('start_deadline', 'desc')
        ->with(['user', 'drivers.user', 'drivers.receives'])->with(['address' => function ($query) {
            $query->withTrashed();
        }]);
        if($this->status == 'pending'){
            $query = $query->where('status', 1);
        }
        elseif($this->status == 'AssignToCar'){
            $query = $query->where('status', 2);
        }
        elseif($this->status == 'collected'){
            $query = $query->where('status', 3);
        }
        elseif($this->status == 'cancelByUser'){
            $query = $query->where('status', 5);
        }
        elseif($this->status == 'cancelByOperator'){
            $query = $query->where('status', 4);
        }
        if($this->driverId){
            $query = $query->whereHas('drivers.user', function (Builder $query) {
                $query->where(function ($query) {
                    $query->where('id',$this->driverId);
                });
            });
        }
        if($this->type == 'citizen'){
            $query = $query->whereHas('user', function (Builder $query) {
                $query->where(function ($query) {
                    $query->where('legal',0);
                });
            });
        }
        elseif($this->type == 'guild'){
            $query = $query->whereHas('user', function (Builder $query) {
                $query->where(function ($query) {
                    $query->where('legal',1);
                });
            });
        }

        if ($this->dateFrom && $this->dateTo) {
            $dateFrom = toGregorian($this->dateFrom,'/','-',false);
            $dateTo = toGregorian($this->dateTo,'/','-',false);

            if($this->status == 'collected'){
                $query->whereHas('drivers', function (Builder $query) use ($dateFrom,$dateTo) {
                    $query->whereBetween('collected_at', [$dateFrom.' 00:00:00', $dateTo.' 23:59:59']);
                });
            }
            else {
                $query->whereBetween('start_deadline', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            }
        }
        elseif ($this->dateFrom && !$this->dateTo) {
            $dateFrom = toGregorian($this->dateFrom,'/','-',false);
            $query->where('start_deadline', '>=',$dateFrom.' 00:00:00');

            if($this->status == 'collected'){
                $query->whereHas('drivers', function (Builder $query) use ($dateFrom) {
                    $query->where('collected_at','>=',$dateFrom.' 00:00:00');
                });
            }
            else {
                $query->where('start_deadline', '>=',$dateFrom.' 00:00:00');
            }

        }
        elseif (!$this->dateFrom && $this->dateTo) {
            $dateTo = toGregorian($this->dateTo,'/','-',false);
            $query->where('start_deadline', '<=',$dateTo.' 23:59:59');

            if($this->status == 'collected'){
                $query->whereHas('drivers', function (Builder $query) use ($dateTo) {
                    $query->where('collected_at','<=',$dateTo.' 00:00:00');
                });
            }
            else {
                $query->where('start_deadline', '<=',$dateTo.' 00:00:00');
            }

        }
        if($this->search){
            $query->where(function ($query){
                $query->whereHas('user', function (Builder $query) {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('guild_title', 'like', '%'.$this->search.'%')
                        ->orWhere('lastname', 'like', '%'.$this->search.'%')
                        ->orWhere('mobile', 'like', '%'.$this->search.'%')
                        ->orWhereRaw("CONCAT(name, ' ', lastname) LIKE '%{$this->search}%'");
                });
            });
        }
        if($this->id){
            $query->where('id',$this->id);
        }
        $row = isset($_COOKIE['table-row']) ? $_COOKIE['table-row'] : $this->row;
        if(auth()->user()->isDeveloper()) {
            //dd($query->toRawSql());
        }
        return (object)[
            'totalAmount' => $query->sum('total_amount'),
            'list' => $query->paginate($row),
        ];
    }

    #[On('search')]
    public function search($search)
    {
        $this->resetPage();
        $this->search = $search;
    }
    #[On('status')]
    public function status($status)
    {
        $this->resetPage();
        $this->status = $status;
    }
    #[On('driverId')]
    public function driverId($driverId)
    {
        $this->resetPage();
        $this->driverId = $driverId;
    }
    #[On('type')]
    public function type($type)
    {
        $this->resetPage();
        $this->type = $type;
    }
    #[On('dateFrom')]
    public function dateFrom($dateFrom)
    {
        $this->resetPage();
        $this->dateFrom = $dateFrom;
    }
    #[On('dateTo')]
    public function dateTo($dateTo)
    {
        $this->resetPage();
        $this->dateTo = $dateTo;
    }

    #[On('row')]
    public function row($row)
    {
        $this->resetPage();
        if(isset($_COOKIE['table-row'])) unset($_COOKIE['table-row']);
        setcookie('table-row',$row,time() + ((86400 * 365))*10, "/"); // 86400 = 1 day
        $this->row = $row;
    }
    #[On('city')]
    public function city($city)
    {
        $this->resetPage();
        $this->city_id = session('city',1) ;
        if ($this->city_id == 0){
            $this->city_id=City::all()->pluck('id')->toArray();
        }
        else{
            $this->city_id=[$this->city_id];
        }
    }
    public function ReferralReward($value)
    {
        $submit = Submit::find($value);
        if ($submit)
        {
            try {
                $exist=BazistWallet::query()->where('type_id',$submit->driver->id)->where('type','submit_user_ref')->first();
                if (!$exist)
                {
                    $submit->rewardForReferral();
                    return sendToast(1,'با موفقیت واریز شد');
                }
                return sendToast(0,'قبلا واریزی انجام شده');
            }catch (Exception $exception)
            {
                event(new ActivityEvent($exception->getMessage(), 'rewardForReferral', false));
                return sendToast(0,'خطا در واریز ');
            }

        }
        return sendToast(0,'موردی برای واریز یافت نشد ');
    }
}
