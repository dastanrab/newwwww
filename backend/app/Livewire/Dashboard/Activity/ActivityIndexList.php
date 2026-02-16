<?php

namespace App\Livewire\Dashboard\Activity;

use App\Events\ActivityEvent;
use App\Models\Activity;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityIndexList extends Component
{
    use WithPagination;
    #[Url]
    public $search;
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    public function render()
    {
        return view('livewire.dashboard.activity.activity-index-list');
    }

    #[Computed]
    public function logs()
    {

        event(new ActivityEvent("خواندن لاگ‌ها"));
        $query = Activity::with('user');
        return $query->latest()->paginate(50);
    }

    #[On('search')]
    public function search($search)
    {
        $this->resetPage();
        $this->search = $search;
    }

    #[On('dateFrom')]
    public function dateFrom($dateFrom)
    {
        $this->resetPage();
        $this->dateFrom = $dateFrom;
    }

    #[On('dateTo')]
    public function dateTo($dateTo)
    {
        $this->resetPage();
        $this->dateTo = $dateTo;
    }
}
