<?php

namespace App\Livewire\Dashboard\Submits;

use App\Models\Polygon;
use App\Models\Submit;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class SubmitMapIndex extends Component
{

    public $breadCrumb = [['نقشه درخواست ها','d.submits.map']];
    #[Title('نقشه درخواست ها')]

    public function render()
    {

        $this->authorize('submit_map_index',Submit::class);
        return view('livewire.dashboard.submits.submit-map-index');
    }

}
