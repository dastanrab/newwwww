<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\Car;
use App\Models\Warehouse;
use App\Models\Recyclable;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StatWarehouseIndexList extends Component
{
    use WithPagination;
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    #[Url]
    public $carId;
    public $row;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-warehouse-index-list');
    }

    #[Computed]
    public function warehouses()
    {
        if ($this->dateFrom && $this->dateTo) {
            $start_date = toGregorian($this->dateFrom,'/','-',false);
            $end_date = toGregorian($this->dateTo,'/','-',false);
        } else {
            $start_date = now()->subDays(2);
            $end_date = now()->addDay();
            $this->dateFrom=now()->subDays(2);
            $this->dateTo=now()->addDay();

        }
        $warehouses = Warehouse::query();
        if ($this->dateFrom && $this->dateTo) {
            $warehouses = $warehouses->whereBetween('received_at', [$start_date, $end_date])->orderBy('received_at', 'desc');
        } else {
            $warehouses = $warehouses->latest();
        }
        if($this->carId){
            $warehouses = $warehouses->where('car_id', $this->carId);
        }
        $row = isset($_COOKIE['table-row']) ? $_COOKIE['table-row'] : $this->row;

        return $warehouses->with(['user', 'car', 'warehouseItem'])->paginate($row);
        //$cars = Car::where('is_active', true)->with('user')->get();
    }

    #[Computed]
    public function recyclables()
    {
        return Recyclable::all();
    }

    #[On('carId')]
    public function carId($carId)
    {
        $this->resetPage();
        $this->carId = $carId;
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
}
