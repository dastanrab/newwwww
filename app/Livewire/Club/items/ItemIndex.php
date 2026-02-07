<?php

namespace App\Livewire\Club\items;

use Livewire\Attributes\Title;
use Livewire\Component;

class ItemIndex extends Component
{

    public $breadCrumb = [
        ['آیتم ها','cl.items']
    ];
    #[Title('آیتم ها')]

    public function render()
    {
        return view('livewire.club.items.item-index');
    }
}
