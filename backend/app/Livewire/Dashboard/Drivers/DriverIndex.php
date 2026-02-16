<?php

namespace App\Livewire\Dashboard\Drivers;

use App\Models\Car;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class DriverIndex extends Component
{
    use WithPagination;
    public $breadCrumb = [['رانندگان','d.drivers']];
    #[Title('رانندگان')]

    public $presentTodayCount = 0;
    public $currentPresentCount = 0;
    public $absentCount = 0;

    public function mount()
    {
        $this->presentTodayCount = Car::where('is_active', true)->where('rollcall_status', '!=', 0)->count();
        $this->currentPresentCount = Car::where('is_active', true)->where('rollcall_status', 2)->count();
        $this->absentCount = Car::where('is_active', true)->where('rollcall_status', 0)->count();
    }

    public function render()
    {
        $this->authorize('user_driver_index', User::class);
        return view('livewire.dashboard.drivers.driver-index');
    }
}
