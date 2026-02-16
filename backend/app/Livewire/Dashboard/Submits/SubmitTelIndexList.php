<?php

namespace App\Livewire\Dashboard\Submits;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class SubmitTelIndexList extends Component
{
    #[Url]
    public $search;
    public function render()
    {
        return view('livewire.dashboard.submits.submit-tel-index-list');
    }

    #[Computed]
    public function users()
    {
        $query = User::query();
        if($this->search) {
            $query = $query->where(function ($query) {
                $query->where('mobile', 'like', '%'.$this->search.'%')
                    ->orWhereRaw("CONCAT(name, ' ', lastname) LIKE '%{$this->search}%'");
            })->orderBy('created_at', 'desc')->take(10)->get();
        }
        else{
            $query = $query->orderBy('created_at', 'desc')->take(10)->get();
        }

        return $query;
    }

    public function selectUser($userId)
    {
        $this->userId = $userId;
        $this->dispatch('step',data : ['step' => 2, 'userId' => $userId]);
    }
}
