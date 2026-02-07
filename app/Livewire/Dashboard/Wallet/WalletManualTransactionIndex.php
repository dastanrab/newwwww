<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\User;
use App\Models\Wallet;
use Hekmatinasser\Verta\Verta;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class WalletManualTransactionIndex extends Component
{
    public $breadCrumb = [['تراکنش های دستی','d.wallet.manual_transaction']];
    #[Title('تراکنش های دستی')]



    public function render()
    {
        $this->authorize('wallet_all_index',Wallet::class);
        return view('livewire.dashboard.wallet.wallet-manual-transaction-index');
    }

    #[Computed]
    public function dates()
    {
        $res = [];
        for ($i=1; $i <= 24; $i++){
            $res[Verta::now()->subMonths($i)->format('Y-m-1')] = Verta::now()->subMonths($i)->format('F Y');
        }
        return $res;
    }
}
