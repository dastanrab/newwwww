<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class StatHighAverageIndex extends Component
{
    use WithPagination;


    public $breadCrumb = [['سایر آمار','d.stats.high-average']];
    #[Title('سایر آمار')]

    public function render()
    {
        return view('livewire.dashboard.stats.stat-high-average-index');
    }

    #[Computed]
    public function users()
    {
        return  getUsersWithAverageWeights();
    }

}
