<?php

namespace App\Livewire\Dashboard\Users;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class UserIndexFilter extends Component
{
    public $search;
    public $role;
    public $isLegal;
    public function render()
    {
        return view('livewire.dashboard.users.user-index-filter');
    }

    public function updated($property)
    {
        if($property == 'search'){
            $this->dispatch('search',$this->search);
        }
        elseif($property == 'isLegal'){
            $this->dispatch('isLegal',$this->isLegal);
        }
    }

    #[On('filter-role')]
    public function role($role)
    {
        $this->role = $role;
    }

    #[On('filter-is-legal')]
    public function isLegal($isLegal)
    {
        $this->isLegal = $isLegal;
    }

    #[On('filter-search')]
    public function search($search)
    {
        $this->search = $search;
    }
}
