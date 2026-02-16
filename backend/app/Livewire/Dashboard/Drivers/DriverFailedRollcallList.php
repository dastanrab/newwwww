<?php

namespace App\Livewire\Dashboard\Drivers;

use App\Models\FailedRollcall;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DriverFailedRollcallList extends Component
{

    public User $driver;

    public function render()
    {
        return view('livewire.dashboard.drivers.driver-failed-rollcall-list');
    }

    #[Computed]
    public function failed()
    {
        $failed = FailedRollcall::where('user_id', $this->driver->id)->orderBy('created_at','DESC')->paginate(20);
        return $failed;
    }

}
