<?php

namespace App\Livewire\Dashboard\Stats;

use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatSubmitIndex extends Component
{
    public $breadCrumb = [['آمار درخواست ها','d.stats.submit']];
    #[Title('آمار درخواست ها')]

    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;

    public function render()
    {
        $this->authorize('stat_submit_index',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-submit-index');
    }
}
