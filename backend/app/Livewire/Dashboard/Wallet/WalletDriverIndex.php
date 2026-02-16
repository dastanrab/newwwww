<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class WalletDriverIndex extends Component
{
    public $breadCrumb = [['کیف پول رانندگان','d.wallet.drivers']];
    public $driverId;
    public $amount;
    #[Title('کیف پول رانندگان')]
    public function render()
    {
        return view('livewire.dashboard.wallet.wallet-driver-index');
    }

    #[Computed]
    public function drivers()
    {
        $drivers = User::with(['cars'])->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })->whereHas('cars', function ($query) {
            $query->where('is_active', 1);
        });
        $drivers = $drivers->orderBy('created_at', 'DESC')->get();
        return $drivers;
    }

    public function create()
    {
        $this->validate([
            'driverId' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1000',
        ]);
        $driver = User::find($this->driverId);
        if($driver->driverWallet){
            sendToast(0,'کیف پول از قبل ایجاد شده است');
        }
        else{
            $driver->driverWallet()->create(['amount' => $this->amount*10, 'city_id' => $driver->city_id]);
            sendToast(1,'کیف پول ایجاد شد');
            $this->reset('amount');
        }

    }
}
