<?php

namespace App\Livewire\Club\Dashboard;

use App\Models\Club;
use App\Models\Offer;
use Livewire\Attributes\Title;
use Livewire\Component;

class Dashboard extends Component
{
    public $breadCrumb = [
        ['داشبورد','cl.dashboard']
    ];
    #[Title('خانه')]

    public $jNow;
    public $wallet;
    public $itemCount;
    public $offerCount;

    public function render()
    {
        $user = auth()->user();
        $clubs = $user->clubs();
        $this->jNow = verta()->format('%d %B %Y');
        $this->itemCount = $clubs->count();
        $this->offerCount = Offer::whereIn('club_id', $clubs->pluck('id')->toArray())->count();
        return view('livewire.club.dashboard.dashboard');
    }
}
