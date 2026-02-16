<?php

namespace App\Livewire\Dashboard\Stats;

use Hekmatinasser\Verta\Verta;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatAttendanceDriverIndexFilter extends Component
{

    #[Url]
    public $dateFrom;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-attendance-driver-index-filter');
    }

    public function updated($prop)
    {
            $this->dispatch('dateFrom',$this->dateFrom);
    }
     #[On('dateFrom')]
    public function dateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }
}
