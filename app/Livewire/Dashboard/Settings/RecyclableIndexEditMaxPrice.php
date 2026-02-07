<?php

namespace App\Livewire\Dashboard\Settings;

use Livewire\Component;

class RecyclableIndexEditMaxPrice extends Component
{

    public $recyclable;
    public $maxPrice;

    public function mount()
    {
        $this->maxPrice = $this->recyclable->max_price;
    }
    public function render()
    {
        return view('livewire.dashboard.settings.recyclable-index-edit-max-price');
    }

    public function update()
    {
        $this->recyclable->update(['max_price' => $this->maxPrice]);
    }
}
