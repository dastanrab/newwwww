<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatWarehouseCreateDriverList extends Component
{
    #[Url]
    public $search;
    public function render()
    {
        return view('livewire.dashboard.stats.stat-warehouse-create-driver-list');
    }

    #[Computed]
    public function drivers()
    {
        $query = User::with('roles')->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })->whereHas('cars', function ($query) {
            $query->where('is_active', 1);
        });
        if($this->search) {
            $query = $query->where(function ($query) {
                $query->where('mobile', 'like', '%'.$this->search.'%')
                    ->orWhereRaw("CONCAT(name, ' ', lastname) LIKE '%{$this->search}%'");
            });
        }
        return $query->orderBy('created_at', 'desc')->get();
    }


    public function selectDriver(User $user)
    {
        $this->dispatch('step',data : ['step' => 2, 'userId' => $user->id]);
    }
}
