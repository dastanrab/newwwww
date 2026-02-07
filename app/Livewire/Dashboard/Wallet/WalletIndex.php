<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\User;
use App\Models\Wallet;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class WalletIndex extends Component
{
    public $breadCrumb = [['کیف پول','d.wallet']];
    #[Title('کیف پول')]

    public $step = 1;
    #[Url]
    public $user_id;

    public function mount()
    {
        if($this->user_id){
            $this->step = 2;
        }
    }

    public function render()
    {
        $this->authorize('wallet_all_index',Wallet::class);
        return view('livewire.dashboard.wallet.wallet-index');
    }

    #[On('get-user-id')]
    function getUserId(User $user)
    {
        $this->user_id = $user->id;
        $this->step = 2;
    }
}
