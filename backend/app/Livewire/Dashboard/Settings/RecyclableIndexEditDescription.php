<?php

namespace App\Livewire\Dashboard\Settings;

use Livewire\Component;

class RecyclableIndexEditDescription extends Component
{
    public $recyclable;
    public $description;

    public function mount()
    {
        $this->description = $this->recyclable->description;
    }

    public function render()
    {
        return view('livewire.dashboard.settings.recyclable-index-edit-description');
    }

    public function update()
    {
        $this->recyclable->update(['description' => $this->description]);
    }
}
