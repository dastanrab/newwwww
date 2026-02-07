<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\Car;
use App\Models\Driver;
use App\Models\Recyclable;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatWarehouseCreateAddWaste extends Component
{
    #[Url]
    public $userId;
    public $user;
    public $basculeBillNumber;
    public $receivedAt;
    public $waste = [];
    public $weight = [];
    public $titles = [];

    public function mount()
    {
        $this->user = User::find($this->userId);
    }
    public function render()
    {
        return view('livewire.dashboard.stats.stat-warehouse-create-add-waste');
    }

    #[Computed]
    public function recyclables()
    {
        return Recyclable::all();
    }

    public function updated($prop)
    {
        if($prop == 'receivedAt'){
            $items = [];
            $this->weight = []; $this->waste = [];
            if($this->driverReceives()) {
                foreach ($this->driverReceives() as $driver) {
                    foreach ($driver->receives as $receive){
                        $items['waste'][$receive->fava_id] = $receive->fava_id;
                        $items['titles'][$receive->fava_id] = $receive->title;
                        $items['weight'][$receive->fava_id] = isset($items['weight'][$receive->fava_id]) ? $items['weight'][$receive->fava_id]+$receive->weight : $receive->weight;
                    }
                }
                $this->waste = array_values($items['waste']);
                $this->weight = array_values($items['weight']);
                $this->titles = array_values($items['titles']);
            }
        }
    }

    public function addWaste()
    {
        $this->waste[] = '';
        $this->weight[] = '';
        $this->titles[] = '';
    }

    #[On('removeWaste')]
    public function removeWaste($waste)
    {
        unset($this->waste[$waste]);
        unset($this->weight[$waste]);
    }

    public function driverReceives()
    {
        $receives = [];
        if($this->receivedAt) {
            $received_at = Verta::parse($this->receivedAt);
            $received_at = $received_at->formatGregorian('Y-m-d');
            $receives = Driver::whereDate('collected_at', $received_at)->where('user_id', $this->user->id)->where('status', 3)->with('receives')->get();
        }
        return $receives;
    }

    public function store()
    {
        $this->validate([
            'basculeBillNumber' => 'required|numeric',
            'receivedAt' => 'required',
        ],
        [
            'basculeBillNumber' => 'شماره قبض باسکول را وارد نمایید',
            'receivedAt' => 'تاریخ را وارد نمایید'
        ]);
        $this->receivedAt = Verta::parse($this->receivedAt)->formatGregorian('Y-m-d');
        $lastDriver = Driver::where('user_id', $this->user->id)->whereDate('collected_at', $this->receivedAt)->latest()->first();
        $car = Car::where('user_id',$this->user->id)->first();
        $warehouse = new Warehouse;
        $warehouse->user_id = auth()->id();
        $warehouse->warehouse_id = 3;
        $warehouse->io = 1;
        $warehouse->car_id = $car->id;
        $warehouse->bascule_bill_number = $this->basculeBillNumber;
        $warehouse->received_at = Carbon::parse($lastDriver->collected_at)->addMinutes(30);
        $warehouse->details = 'WarehouseItem';
        $warehouse->save();

        $recyclables_id = $this->waste;
        $recyclables_weight = $this->weight;
        $recyclables_title = $this->titles;
        $recyclables = [];
        foreach ($recyclables_id as $key => $value) {
            array_push($recyclables, ['GoodRef' => $value, 'Quantity' => $recyclables_weight[$key]]);
            $warehouse_item = new WarehouseItem;
            $warehouse_item->warehouse_id = $warehouse->id;
            $warehouse_item->title = $recyclables_title[$key];
            $warehouse_item->weight = $recyclables_weight[$key];
            $warehouse_item->save();
        }

        $details = json_encode($recyclables);

        $warehouse->details = $details;
        $warehouse->save();

        $this->redirectRoute('d.stats.warehouse');
    }
}
