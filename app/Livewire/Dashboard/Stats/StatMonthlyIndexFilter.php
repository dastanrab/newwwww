<?php

namespace App\Livewire\Dashboard\Stats;

use Hekmatinasser\Verta\Verta;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatMonthlyIndexFilter extends Component
{

    #[Url]
    public $date;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-monthly-index-filter');
    }

    #[Computed]
    public function dates()
    {
        $res = [];
        for ($i=1; $i <= 24; $i++){
            $res[Verta::now()->subMonths($i)->format('Y-m-1')] = Verta::now()->subMonths($i)->format('F Y');
        }
        return $res;
    }
}
