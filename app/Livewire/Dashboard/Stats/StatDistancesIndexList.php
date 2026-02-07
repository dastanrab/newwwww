<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StatDistancesIndexList extends Component
{

    use WithPagination;
    #[Url]
    public $date;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-distances-index-list');
    }

    #[Computed]
    public function drivers()
    {
        if($this->date){
            $date = verta()->parse($this->date)->toCarbon()->format('Y-m-d');
        }
        else{
            $date = today();
        }
        $users = User::with(['roles','cars','drivers.submit.address','drivers' => function ($query) use($date) {
            $query->whereDate('collected_at', $date)->where('status',3);
        }])->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })->whereHas('cars', function ($query) {
            $query->where('is_active', 1);
        })->orderByDesc('created_at')->paginate(20);
        return $users;
    }

    #[On('date')]
    public function date($date)
    {
        $this->resetPage();
        $this->date = $date;
    }
}
