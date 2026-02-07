<?php

namespace App\Livewire\Dashboard\Drivers;

use Livewire\Attributes\On;
use Livewire\Component;

class DriverIndexFilter extends Component
{
    public $search;
    public $status;
    public $rollCallStatus;
    public function render()
    {
        return view('livewire.dashboard.drivers.driver-index-filter');
    }

    public function updated($property)
    {
        if($property == 'search'){
            $this->dispatch('search',$this->search);
        }
        elseif($property == 'rollCallStatus'){
            $this->dispatch('rollCallStatus',$this->rollCallStatus);
        }
    }

    #[On('filter-status')]
    public function status($status)
    {
        $this->status = $status;
    }

    #[On('filter-search')]
    public function search($search)
    {
        $this->search = $search;
    }

    #[On('filter-roll-call-status')]
    public function rollCallStatus($rollCallStatus)
    {
        $this->rollCallStatus = $rollCallStatus;
    }
}
