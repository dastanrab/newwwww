<?php

namespace App\Livewire\Dashboard\Club;

use App\Models\Club;
use App\Models\ScoreHistory;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

class ScoreHistoriesIndex extends Component
{
    public $breadCrumb = [['باشگاه مشتریان','d.club.items']];
    /**
     * @var mixed[]
     */
    public $scores;
    public $users;

    #[Title('باشگاه مشتریان > سابقه امتیاز ها')]
    public function mount()
    {
        $this->scores = ScoreHistory::query()->with('user')->orderBy('id', 'desc')->limit(50)->get();
        $this->users = User::query()->select(['id', 'name','lastname','score'])->orderBy('score','desc')->limit(50)->get();

    }
    public function render()
    {
        $this->authorize('club_offer_index', Club::class);
        return view('livewire.dashboard.club.club-score-histories-index');
    }

}
