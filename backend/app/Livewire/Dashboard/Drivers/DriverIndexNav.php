<?php

namespace App\Livewire\Dashboard\Drivers;

use App\Models\Car;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DriverIndexNav extends Component
{
    #[Url]
    public $status;
    public $activeCount;
    public $deActiveCount;

    public function mount()
    {
        $this->activeCount = Car::where('is_active', true)->count();
        $this->deActiveCount = Car::where('is_active', false)->count();
    }

    public function render()
    {
        return view('livewire.dashboard.drivers.driver-index-nav');
    }

    public function filterStatus($value)
    {
        $this->status = $value;
        $this->dispatch('status', status : $value);
    }
}
