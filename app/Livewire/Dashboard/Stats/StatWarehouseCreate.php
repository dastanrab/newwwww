<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatWarehouseCreate extends Component
{

    public $breadCrumb = [['آمار کلی انبار', 'd.stats.warehouse'], ['افزودن قبض']];
    #[Title('آمار کلی انبار > افزودن قبض')]

    public $step = 1;
    #[Url]
    public $userId = null;
    #[Url]
    public $search;
    public function render()
    {
        $this->authorize('stat_warehouse_create',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-warehouse-create');
    }

    #[On('step')]
    public function step($data)
    {
        $this->step = $data['step'];
        $this->userId = $data['userId'];
        $this->reset('search');
    }
}
