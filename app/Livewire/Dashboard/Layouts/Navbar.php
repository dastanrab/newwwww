<?php

namespace App\Livewire\Dashboard\Layouts;

use Livewire\Component;

class Navbar extends Component
{
    public $breadCrumb;
    public function render()
    {
        return view('livewire.dashboard.layouts.navbar');
    }
}
