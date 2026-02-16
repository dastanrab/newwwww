<?php

namespace App\Livewire\Dashboard\Settings;

use App\Models\Polygon;
use Exception;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class MapIndex extends Component
{
    public $city=1;
    public $breadCrumb = [['نقشه','d.settings.map']];
    #[Title('نقشه')]

    public function render()
    {
        $this->authorize('setting_map_index',MapIndex::class);
        return view('livewire.dashboard.settings.map-index',[
        'options' => [
        '1' => 'مشهد',
        '3' => 'طرقبه'
    ],
        ]);
    }

    #[Computed]
    public function polygons()
    {
        return Polygon::all();
    }

    #[On('storePolygon')]
    public function storePolygon($data)
    {

        $polygon = [];
        if(!empty($data['properties']['region']) && !empty($data['properties']['color'])) {
            foreach ($data['geometry']['coordinates'][0] as $item) {
                $polygon[] = [(float)$item[1], (float)$item[0]];
            }
            $polygon = json_encode($polygon);
            $middle = json_encode([(float)$data['properties']['middle']['lat'], (float)$data['properties']['middle']['lng']]);
            try {
                $lastPolygon = Polygon::orderBy('sort','DESC')->first();
                $polygon = Polygon::create([
                    'region' => $data['properties']['region'],
                    'polygon' => $polygon,
                    'middle' => $middle,
                    'color' => $data['properties']['color'],
                    'sort' => $lastPolygon->sort+1,
                    'city_id' => $this->city,
                ]);
                for ($i=1;$i<=7;$i++){
                    $j = 1;
                    $polygon->polygonDayHours()->create([
                        'city_id' => $this->city,
                        'day_id'  => $i,
                        'hour_id' => $j,
                        'status'  => 0,
                    ]);
                    for ($j=2;$j<=5;$j++){
                        $polygon->polygonDayHours()->create([
                            'city_id' => $this->city,
                            'day_id'  => $i,
                            'hour_id' => $j,
                            'status'  => 0,
                        ]);
                    }
                }

            }
            catch (Exception $e){

            }
        }

    }

    #[On('updatePolygon')]
    public function updatePolygon($data)
    {

        if(!empty($data['properties']['newRegion'])) {
            $polygon = Polygon::where('region', $data['properties']['lastRegion'])->first();
            $newPolygon = [];
            foreach ($data['geometry']['coordinates'][0] as $item) {
                $newPolygon[] = [(float)$item[1], (float)$item[0]];
            }
            $newPolygon = json_encode($newPolygon);
            $newMiddle = json_encode([(float)$data['properties']['middle']['lat'], (float)$data['properties']['middle']['lng']]);
            $polygon->update([
                'region' => $data['properties']['newRegion'],
                'polygon' => $newPolygon,
                'middle' => $newMiddle,
            ]);

            // [[36.3772060,59.5267110],[36.3709173,59.5331095],[36.3624162,59.5385167],[36.3454113,59.5480344],[36.3482630,59.5652738],[36.3497147,59.5708965],[36.3498703,59.5755859],[36.3488506,59.5794796],[36.3472779,59.5829558],[36.3434756,59.5903924],[36.3662176,59.6153354],[36.4015272,59.5527649],[36.3772060,59.5267110]]
        }
    }
    #[On('city')]
    public function city($city)
    {
      $this->city=(int)$city;
    }
    #[On('deletePolygon')]
    public function deletePolygon($region)
    {
        if(auth()->user()->isDeveloper()){
            $polygon = Polygon::where('region',$region)->first();
            $polygon->polygonDayHours()->delete();
            $polygon->delete();
        }
    }
}
