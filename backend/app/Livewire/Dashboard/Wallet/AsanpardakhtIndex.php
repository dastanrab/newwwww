<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\Wallet;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class AsanpardakhtIndex extends Component
{

    public $breadCrumb = [['آسان پرداخت','d.wallet.ap']];
    #[Title('آسان پرداخت')]

    #[Url]
    public $status;
    public $statusTitle = 'واریز/برداشت';

    public function mount()
    {
        $this->statusTitle($this->status);
    }

    public function render()
    {
        $this->authorize('wallet_ap_index',Wallet::class);
        return view('livewire.dashboard.wallet.asanpardakht-index');
    }

    #[On('status')]
    public function status($status)
    {
        $this->statusTitle($status);
    }

    public function statusTitle($status)
    {
        if($status == 'deposit'){
            $this->statusTitle = 'واریز';
        }
        elseif($status == 'withdraw'){
            $this->statusTitle = 'برداشت';
        }elseif($status == 'sharj'){
            $this->statusTitle = 'واریزی مخزن';
        }
        else{
            $this->statusTitle = 'واریز/برداشت';
        }
    }

    #[On('reload-page')]
    public function reloadPage()
    {
        $this->reset();
    }
}
