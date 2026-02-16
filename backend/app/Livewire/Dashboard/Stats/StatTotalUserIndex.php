<?php

namespace App\Livewire\Dashboard\Stats;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class StatTotalUserIndex extends Component
{
    public $breadCrumb = [['آمار تعداد کاربران','d.stats.daily']];
    #[Title('آمار تعداد کاربران')]
    public function render()
    {
        $this->authorize('stat_total_user_index',StatSubmitIndex::class);
        return view('livewire.dashboard.stats.stat-total-user-index');
    }

    #[Computed]
    public function stats()
    {
        $users = DB::table('users')->selectRaw('(select count(*) FROM users WHERE legal = 1) as legal, (select count(*) FROM users WHERE legal = 0) as not_legal')->first();
        return $users;
    }

}
