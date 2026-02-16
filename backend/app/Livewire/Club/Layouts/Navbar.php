<?php

namespace App\Livewire\Club\Layouts;

use Livewire\Component;

class Navbar extends Component
{
    public $breadCrumb;
    public function render()
    {
        return view('livewire.club.layouts.navbar');
    }
}
