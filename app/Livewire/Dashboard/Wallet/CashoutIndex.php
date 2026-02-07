<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\Cashout;
use App\Models\Wallet;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class CashoutIndex extends Component
{

    public $breadCrumb = [['درخواست واریز به کارت','d.wallet.cashout']];
    #[Title('درخواست واریز به کارت')]

    #[Url]
    public $status;
    public $totalAmount;
    public $title;

    public function render()
    {
        $this->authorize('cashout_all_index',Wallet::class);

        return view('livewire.dashboard.wallet.cashout-index');
    }

    #[On('cashoutTotalAmount')]
    public function cashoutTotalAmount($data)
    {
        $this->totalAmount = $data['totalAmount'];
        $this->title = $data['title'];
    }
}
