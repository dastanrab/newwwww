<?php

namespace App\Livewire\Dashboard\Submits;


use Livewire\Component;

class DriverMapIndexModal extends Component
{
    public $driverinfo;
    public function render()
    {
        return view('livewire.dashboard.submits.driver-map-index-modal');
    }

}
