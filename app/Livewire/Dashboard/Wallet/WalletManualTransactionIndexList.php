<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\BazistWallet;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class WalletManualTransactionIndexList extends Component
{
    use WithPagination;
    #[Url]
    public $search;
    #[Url]
    public $dateFrom;
    public $userId;
    public $amount;
    public $description;
    public $type;
    public $submit_id;
    public $ref;
    public $fee = 700;
    public function render()
    {
        return view('livewire.dashboard.wallet.wallet-manual-transaction-index-list');
    }

    #[Computed]
    public function transactions()
    {
        if (isset($this->dateFrom))
        {
            $start_month = \Hekmatinasser\Verta\Verta::parse($this->dateFrom);
            $start_date = \Carbon\Carbon::instance($start_month->startMonth()->datetime());
            $end_month = $start_month->endMonth();
            $end_date = \Carbon\Carbon::instance($end_month->datetime());
        }
        else{
            $end_date = carbon::now();
            $start_date = carbon::now()->subDays(30);
        }
        $transactions=BazistWallet::query()->with(['user'])->whereIn('type',['deposit','withdraw_bazist_wallet'])->whereBetween('created_at',[$start_date->format('Y-m-d').' 00:00:00',$end_date->format('Y-m-d').' 23:59:59']);
        return $transactions->orderBy('created_at', 'desc')->paginate(20);
    }

    #[On('dateFrom')]
    public function dateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

}
