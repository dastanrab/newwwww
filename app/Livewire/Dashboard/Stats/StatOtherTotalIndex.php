<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class StatOtherTotalIndex extends Component
{
    use WithPagination;


    public $breadCrumb = [['آمار پویا','d.stats.other-total']];
    #[Title('آمار پویا')]

    public function render()
    {
        return view('livewire.dashboard.stats.stat-total-other-index');
    }

}
