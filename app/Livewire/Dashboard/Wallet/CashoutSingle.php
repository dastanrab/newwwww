<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\Cashout;
use App\Models\Wallet;
use Livewire\Attributes\Title;
use Livewire\Component;

class CashoutSingle extends Component
{
    public $breadCrumb = [['درخواست واریز به کارت','d.wallet.cashout'],['ویرایش']];
    #[Title('درخواست واریز به کارت > ویرایش')]

    public Cashout $cashout;
    public $cardNumber;
    public $shabaNumber;
    public $name;
    public $bank;
    public $traceCode;
    public $status;

    public function mount()
    {
        $this->cardNumber = $this->cashout->card_number;
        $this->shabaNumber = $this->cashout->shaba_number;
        $this->name = $this->cashout->name;
        $this->bank = $this->cashout->bank;
        $this->traceCode = $this->cashout->trace_code;
        $this->status = $this->cashout->status;
    }

    public function render()
    {
        $this->authorize('cashout_all_single',Wallet::class);
        return view('livewire.dashboard.wallet.cashout-single');
    }

    public function save()
    {
        $this->cashout->update([
            'card_number' => $this->cardNumber,
            'shaba_number' => $this->shabaNumber,
            'name' => $this->name,
            'bank' => $this->bank,
            'trace_code' => $this->traceCode,
            'status' => $this->status,
        ]);
        sendToast(1,'با موفقیت ذخیره شد');
    }
}
