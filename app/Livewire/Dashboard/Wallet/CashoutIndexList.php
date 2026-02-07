<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\BankSaman;
use App\Models\BazistWallet;
use App\Models\Cashout;
use App\Models\Wallet;
use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CashoutIndexList extends Component
{
    use WithPagination;
    #[Url]
    public $status;
    #[Url]
    public $search;
    #[Url]
    public $date;
    public function render()
    {
        return view('livewire.dashboard.wallet.cashout-index-list');
    }
    #[Computed]
    public function cashouts()
    {
        $cashouts = Cashout::query();

        if($this->search){
            $cashouts->whereHas('user', function (Builder $query) {
                $query->where(function ($query) {
                    $query->where('mobile','LIKE',"%{$this->search}%")
                        ->orWhereRaw("CONCAT(name, ' ', lastname) LIKE '%{$this->search}%'")
                        ->orWhere('guild_title', 'like', "%{$this->search}%")
                        ->orWhere('mobile', 'like', "%{$this->search}%")
                        ->orWhere('trace_code', $this->search);
                });
            });
        }
        if ($this->date) {
           $date= Verta::parse($this->date)->toCarbon()->format('Y-m-d');
            $cashouts->whereDate('updated_at', $date);
        }

        if($this->status == 'waitingDeposit'){
            $cashouts = $cashouts->where('status', 'depositing')->orderBy('id','ASC');
            $this->dispatch('cashoutTotalAmount',['title' => 'لیست درانتظار واریز', 'totalAmount' => $cashouts->sum('amount')]);
        }
        elseif($this->status == 'deposited'){
            $cashouts = $cashouts->where('status', '=', 'deposited')->orderBy('id','DESC');
            $this->dispatch('cashoutTotalAmount',['title' => 'لیست واریز شده', 'totalAmount' => $cashouts->sum('amount')]);
        }
        else{
            $cashouts = $cashouts->where('status', 'waiting')->orderBy('id','ASC');
            $this->dispatch('cashoutTotalAmount',['title' => 'لیست واریز نشده', 'totalAmount' => $cashouts->sum('amount')]);
        }
        return $cashouts->paginate(20);
    }

    #[On('status')]
    public function status($value)
    {
        $this->resetPage();
        $this->status = $value;
    }

    #[On('search')]
    public function search($value)
    {
        $this->resetPage();
        $this->search = $value;
    }

    #[On('refund-to-wallet')]
    public function refundToWallet(Cashout $cashout)
    {
        /*if($cashout->trace_code == 0){
            sendToast(0,'قبلا به بانک ارسال شده');
        }
        else {*/
            $city_id = 1;
            $user_id = $cashout->user_id;
            $amount = $cashout->amount;
            $wallet = Wallet::where('user_id', $user_id)->first();
            BazistWallet::create(
                $city_id,
                $user_id,
                $wallet->id,
                'back_to_bazist_wallet',
                $wallet->id,
                $amount * 10, // Rial
                $wallet->wallet + $amount,
                'واریز',
                'برگشت از درخواست واریز به کیف پول بازیست'
            );
            $wallet->wallet += $amount; // Toman
            $wallet->save();
            $cashout->status = 'refunded';
            $cashout->save();
        /*}*/

    }

    #[On('send-to-bank')]
    public function sendToBank(Cashout $cashout)
    {
        if ($cashout->bank || $cashout->trace_code) {
            sendToast(0,'قبلا به بانک ارسال شده');
        } else {
            if (App::environment('production')) {
                $result = BankSaman::confirm($cashout->amount * 10, 1, $cashout->amount * 10, 'IR' . $cashout->shaba_number, $cashout->name);
                $bank = 'SB24';
                $bankId = $result['result']['orderId'];
            }
            else{
                $bank = 'SB24-LOCAL';
                $bankId = rand(1324324,378371298789732);
            }
            $cashout->bank = $bank;
            $cashout->trace_code = 0;
            $cashout->bank_id = $bankId;
            $cashout->status = 'depositing';
            $cashout->save();
            sendToast(1,'با موفقیت برای بانک ارسال شد');
        }
    }
}
