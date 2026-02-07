<?php

namespace App\Livewire\Dashboard\Settings;

use App\Models\Polygon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class MapSortRegionIndex extends Component
{
    public $breadCrumb = [['مرتب سازی نقشه','d.settings.region']];
    #[Title('مرتب سازی نقشه')]

    public function render()
    {
        $this->authorize('setting_map_index',MapIndex::class);
        return view('livewire.dashboard.settings.map-sort-region-index');
    }

    #[Computed]
    public function polygons()
    {
        return Polygon::orderBy('sort')->get();
    }

    #[On('sortRegionId')]
    public function sortRegionId($ids)
    {
        $i = 1;
        foreach ($ids as $id){
            Polygon::find($id)->update(['sort' => $i]);
            $i++;
        }
        sendToast(1,'با موفقیت ذخیره شد');
    }
}
