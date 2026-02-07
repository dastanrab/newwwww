<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Component;

class StatUserIndex extends Component
{
    public $breadCrumb = [['آمار کاربران','d.stats.user']];
    #[Title('آمار کاربران')]
    public function render()
    {
        $this->authorize('stat_user_index',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-user-index');
    }
}
