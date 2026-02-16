<?php

namespace App\Livewire\Dashboard\Users;

use App\Models\Isun;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class UserSingleOrders extends Component
{

    use WithPagination,WithoutUrlPagination;

    public $user;
    public function render()
    {
        return view('livewire.dashboard.users.user-single-orders');
    }

    #[Computed]
    public function orders()
    {
        $query = Isun::query();
        $query->where('user_id',$this->user->id)
            ->orderBy('created_at', 'desc');
        return $query->paginate(10);
    }
}
