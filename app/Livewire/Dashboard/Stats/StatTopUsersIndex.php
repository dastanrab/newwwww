<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class StatTopUsersIndex extends Component
{
    use WithPagination;


    public $breadCrumb = [['کاربران منتخب','d.stats.top-users']];
    #[Title('سایر آمار')]

    public function render()
    {
        return view('livewire.dashboard.stats.stat-top-users-index');
    }

    #[Computed]
    public function users()
    {
        return  getTopUsers();
    }

}
