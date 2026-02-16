<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\AsanPardakht;
use App\Models\DriverWallet;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class WalletDriverIndexList extends Component
{
    use WithPagination;
    #[Url]
    public $search;
    public $row = 7;
    public $amount;
    public function render()
    {
        return view('livewire.dashboard.wallet.wallet-driver-index-list');
    }

    #[Computed]
    public function drivers()
    {
        $query = DriverWallet::query();

        if($this->search){
            $query->whereHas('user', function (Builder $query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('guild_title', 'like', '%'.$this->search.'%')
                        ->orWhere('lastname', 'like', '%'.$this->search.'%')
                        ->orWhere('mobile', 'like', '%'.$this->search.'%')
                        ->orWhereRaw("CONCAT(name, ' ', lastname) LIKE '%{$this->search}%'");
                });
            });
        }
        $row = isset($_COOKIE['table-row']) ? $_COOKIE['table-row'] : $this->row;
        return $query->latest()->paginate($row);
    }

    public function deposit(User $user)
    {
        $this->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $user->driverWallet->update(['amount' => $user->driverWallet->amount + ($this->amount*10)]);
        sendToast(1,'اعتبار افزایش یافت');
        $this->reset('amount');
    }

    #[On('search')]
    public function search($search)
    {
        $this->search = $search;
        $this->resetPage();
    }

    #[On('row')]
    public function row($row)
    {
        $this->resetPage();
        if(isset($_COOKIE['table-row'])) unset($_COOKIE['table-row']);
        setcookie('table-row',$row,time() + ((86400 * 365))*10, "/"); // 86400 = 1 day
        $this->row = $row;
    }
}
