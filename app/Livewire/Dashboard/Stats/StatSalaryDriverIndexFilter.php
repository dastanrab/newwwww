<?php

namespace App\Livewire\Dashboard\Stats;

use Hekmatinasser\Verta\Verta;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatSalaryDriverIndexFilter extends Component
{

    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-salary-driver-index-filter');
    }

    public function updated($prop)
    {
//        if($prop == 'dateFrom'){
//            $this->dispatch('dateFrom',$this->dateFrom);
//        }
//        elseif($prop == 'dateTo'){
//            $this->dispatch('dateTo',$this->dateTo);
//        }
    }
    public function filter()
    {
        $this->dispatch('dateFrom',$this->dateFrom);
    }
}
