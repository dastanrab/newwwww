<?php

namespace App\Livewire\Dashboard\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;
    public  $breadCrumb = [['کاربران','d.users']];
    #[Title('کاربران')]
    public function render()
    {
        $this->authorize('user_index', User::class);
        return view('livewire.dashboard.users.user-index');
    }

}
