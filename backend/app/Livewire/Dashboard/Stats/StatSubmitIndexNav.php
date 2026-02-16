<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Url;
use Livewire\Component;

class StatSubmitIndexNav extends Component
{
    #[Url]
    public $status;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-submit-index-nav');
    }

    public function filterStatus($status)
    {
        $this->status = $status;
        $this->dispatch('status',$status);
    }
}
