<?php

namespace App\Livewire\Dashboard\Home;


use Livewire\Attributes\Title;
use Livewire\Component;

class ChartsManagerIndex extends Component
{
    public $breadCrumb = [['نمودار ها','chart']];
    #[Title('نمودار ها')]

    public function render()
    {
        return view('livewire.dashboard.home.charts-manager-index');
    }
}
