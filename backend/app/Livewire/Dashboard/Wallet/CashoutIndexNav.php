<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\Cashout;
use Livewire\Attributes\Url;
use Livewire\Component;

class CashoutIndexNav extends Component
{
    #[Url]
    public $status;
    public $notDeposited = 0;
    public $waitingDeposit = 0;
    public $deposited = 0;
    public function render()
    {

        $this->waitingDeposit = Cashout::where('status', 'depositing')->orderBy('id','ASC')->count();
        $this->deposited = Cashout::where('status', 'deposited')->orderBy('id','DESC')->count();
        $this->notDeposited = Cashout::where('status', 'waiting')->orderBy('id','ASC')->count();

        return view('livewire.dashboard.wallet.cashout-index-nav');
    }

    public function filterStatus($status)
    {
        $this->status = $status;
        $this->dispatch('status',$status);
    }
}
