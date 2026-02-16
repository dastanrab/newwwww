<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\Driver;
use App\Models\Recyclable;
use App\Models\User;
use App\Models\WarehouseDaily;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatWarehouseDriverIndexListSubmit extends Component
{

    public $breadCrumb = [['آمار بار رانندگان','d.stats.warehouse-driver']];
    #[Title('آمار بار رانندگان')]
    public User $user;
    #[Url]
    public $date;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-warehouse-driver-index-list-submit');
    }

    #[Computed]
    public function collected()
    {
        $last_warehouse = WarehouseDaily::where('user_id', $this->user->id)->latest()->first();
        $collected = Driver::where('user_id', $this->user->id)->where('status', 3);
        if($this->date){
            $collected = $collected->whereDate('collected_at', verta()->parse($this->date)->toCarbon());
        }
        else{
            $collected = $collected->where('collected_at', '>', Carbon::parse($last_warehouse->created_at));
        }
        return $collected->orderBy('collected_at','desc')->with('user', 'receives')->get();
    }

    #[Computed]
    public function recyclables()
    {
        return Recyclable::all();
    }

    public function store()
    {
        $warehouse = new WarehouseDaily;
        $warehouse->user_id = $this->user->id;
        $total_weight=count($this->collected) > 0 ? $this->collected->pluck('weights')->sum() : 0;
        if(!in_array(auth()->id(),User::warehouserId())){
            sendToast(0,'شما مجاز به ثبت نیستید');
        }
        else {
//            if (auth()->id() == 60868) { // اگر کلالی بود باید به نام انبار ازادی ثبت شود
//                // از تاریخ ۱۹/۰۱/۱۴۰۳ جواد کلالی واسه انبار آزادی ثبت میزنه
//                $operator_id = User::azadiId();
//            } else {
//                $operator_id = auth()->id();
//            }
            $warehouse->operator_id = auth()->id();
            /*if (developerId() == auth()->id()) {
                dd($warehouse);
            }*/
            if ($total_weight>0){
                $warehouse->weight=$total_weight;
                $warehouse->save();
            }

            sendToast(1, 'با موفقیت ثبت شد');
        }
    }
}
