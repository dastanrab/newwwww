<?php

namespace App\Livewire\Dashboard\Drivers;

use App\Models\Car;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DriverIndexList extends Component
{

    use WithPagination;
    #[Url(as: 'q', history: true)]
    public $search;
    #[Url]
    public $status;
    #[Url]
    public $rollCallStatus;
    public $row = 7;

    public function render()
    {
        return view('livewire.dashboard.drivers.driver-index-list');
    }

    #[Computed]
    public function drivers()
    {
        if(!empty($this->status)){
            $this->dispatch('filter-status',$this->status);
            $isActive = $this->status == 'active' ? 1 : 0;
        }
        else{
            $isActive = 1;
        }
        $query = User::with('roles','cars')->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        });
        if($this->status != 'all'){
            $query->whereHas('cars', function ($query) use($isActive) {
                $query->where('is_active', $isActive);
            });
        }
        if(!empty($this->rollCallStatus && $this->status == 'active')){
            $this->dispatch('filter-roll-call-status',$this->rollCallStatus);

            $rollCallStatus = $this->rollCallStatus;
            $query->whereHas('cars', function ($query) use($rollCallStatus) {
                if($rollCallStatus == 'presentToday'){
                    $query->where('rollcall_status', '!=', 0);
                }
                elseif($rollCallStatus == 'currentPresent'){
                    $query->where('rollcall_status', 2);
                }
                elseif($rollCallStatus == 'absent'){
                    $query->where('rollcall_status', 0);
                }
            });
        }
        if(!empty($this->search)){
            $this->dispatch('filter-search',$this->search);
            if(is_numeric($this->search)){
                $query->where('mobile','LIKE',"%{$this->search}%");
            }
            else{
                $query->whereRaw("CONCAT(name, ' ', lastname) LIKE '%{$this->search}%'");
            }
        }
        $row = isset($_COOKIE['table-row']) ? $_COOKIE['table-row'] : $this->row;
        return $query->orderBy('created_at', 'desc')->paginate($row);
    }

    #[On('search')]
    public function search($search)
    {
        $this->resetPage();
        $this->search = $search;
    }
    #[On('rollCallStatus')]
    public function rollCallStatus($rollCallStatus)
    {
        $this->resetPage();
        $this->rollCallStatus = $rollCallStatus;
    }

    #[On('status')]
    public function status($status)
    {
        $this->resetPage();
        $this->status = $status;
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
