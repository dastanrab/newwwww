<?php

namespace App\Livewire\Dashboard\Supervisor;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SupervisorDriverIndexList extends Component
{

    use WithPagination;
    #[Url(as: 'q', history: true)]
    public $search;
    #[Url]
    public $status;
    #[Url]
    public $rollCallStatus;
    public $row = 20;

    public function render()
    {
        return view('livewire.dashboard.supervisor.supervisor-driver-index-list');
    }

    #[Computed]
    public function drivers()
    {
        $query = User::with('roles','cars')->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        });
        if($this->status != 'all'){
            $query->whereHas('cars', function ($query) {
                $query->where('is_active', 1);
            });
        }
        if(empty($this->status) || $this->status == 'present'){
            $query->whereHas('cars', function ($query)  {
                $query->where('rollcall_status', '!=', 0);

            });
        }
        elseif($this->status == 'absent'){
            $query->whereHas('cars', function ($query)  {
                $query->where('rollcall_status', '=', 0);

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
        return $query->orderBy('created_at', 'desc')->paginate($this->row);
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
