<?php

namespace App\Livewire\Dashboard\Settings;

use App\Models\Driver;
use App\Models\Polygon;
use App\Models\PolygonDriver;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AreaIndex extends Component
{
    use WithPagination;
    #[Url]
    public $search;
    public $polygons;
    public $drivers;
    public $breadCrumb = [['مناطق بازیست','d.settings.area']];
    #[Title('مناطق بازیست')]
    public function render()
    {

        $this->authorize('setting_area_index',AreaIndex::class);
        return view('livewire.dashboard.settings.area-index');
    }

    #[Computed]
    public function mount()
    {

        $this->polygons = Polygon::orderBy('sort')->get();
        $this->drivers = $this->drivers();
    }

    #[Computed]
    public function drivers()
    {
        $query = User::with(['roles','polygonDrivers.polygon'])->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })->whereHas('cars', function ($query) {
            $query->where('is_active', 1);
        });
        if($this->search){
            $query = $query->whereRaw("(mobile LIKE '%{$this->search}%' OR CONCAT(name, ' ', lastname) LIKE '%{$this->search}%')");

        }
        $users = $query->orderBy('created_at', 'DESC')->get()/*->paginate(50)*/;
        return $users;
    }

    public function polygonSelect(User $driver, Polygon $polygon)
    {
        $pd = PolygonDriver::where('user_id',$driver->id)->where('polygon_id', $polygon->id)->first();
        if($pd){
            $pd->delete();
        }
        else{
            PolygonDriver::create(['user_id' => $driver->id, 'polygon_id' => $polygon->id]);
        }
    }

    #[On('search')]
    public function search($search)
    {
        $this->resetPage();
        $this->search = $search;
        $this->drivers = $this->drivers();
    }
}
