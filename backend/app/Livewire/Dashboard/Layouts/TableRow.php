<?php

namespace App\Livewire\Dashboard\Layouts;

use Livewire\Component;

class TableRow extends Component
{
    public $rows = [7, 10, 25, 50, 75, 100];
    public $row = null;

    public function mount(){
        $this->row = $_COOKIE['table-row'] ?? $this->rows[0];
    }

    public function render()
    {
        return view('livewire.dashboard.layouts.table-row');
    }
}
