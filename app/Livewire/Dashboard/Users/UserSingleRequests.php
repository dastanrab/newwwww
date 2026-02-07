<?php

namespace App\Livewire\Dashboard\Users;

use App\Models\Submit;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class UserSingleRequests extends Component
{
    use WithPagination,WithoutUrlPagination;

    public $user;

    public function render()
    {
        return view('livewire.dashboard.users.user-single-requests');
    }

    #[Computed]
    public function requests()
    {
        $query = Submit::query();
        $query->where('user_id',$this->user->id)
            ->orderBy('created_at', 'desc')
            ->with(['user', 'drivers.user', 'drivers.receives'])
            ->with(['address' => function ($query) {
                $query->withTrashed();
            }]);
        return $query->paginate(10);
    }

}
