<?php

namespace App\Livewire\Dashboard\Settings;

use App\Models\Recyclable;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class RecyclableIndex extends Component
{

    public $breadCrumb = [['دسته بندی پسماندها','d.settings.recyclable']];
    #[Title('دسته بندی پسماندها')]
    public function render()
    {
        $this->authorize('setting_recyclable_index',RecyclableIndex::class);
        return view('livewire.dashboard.settings.recyclable-index');
    }

    #[Computed]
    public function recyclables()
    {
        return Recyclable::with('percentages')->get();
    }
}
