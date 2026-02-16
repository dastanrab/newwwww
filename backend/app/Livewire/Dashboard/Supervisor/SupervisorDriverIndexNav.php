<?php

namespace App\Livewire\Dashboard\Supervisor;

use App\Models\Car;
use Livewire\Attributes\Url;
use Livewire\Component;

class SupervisorDriverIndexNav extends Component
{
    #[Url]
    public $status;

    public function render()
    {
        return view('livewire.dashboard.supervisor.supervisor-driver-index-nav');
    }

    public function filterStatus($value)
    {
        $this->status = $value;
        $this->dispatch('status', status : $value);
    }
}
