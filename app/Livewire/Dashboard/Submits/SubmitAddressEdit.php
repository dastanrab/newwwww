<?php

namespace App\Livewire\Dashboard\Submits;

use App\Models\Address;
use App\Models\Polygon;
use App\Models\Submit;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Title;
use Livewire\Component;

class SubmitAddressEdit extends Component
{
    public $breadCrumb = [['درخواست ها','d.submits.all'], ['ویرایش آدرس','d.address.edit']];
    #[Title('درخواست ها > ویرایش آدرس')]

    public $addresses = [];
    public $user;
    public $lat;
    public $lng;
    public Address $address;
    public $address_text;
    public $full_address;

    public function boot()
    {
        $this->user = $this->address->user;
        $this->full_address = $this->address->address;
    }

    public function render()
    {
        return view('livewire.dashboard.submits.submit-address-edit');
    }

    public function updated($prop)
    {

        if($prop == 'lat'){
            $this->addresses= [];
            $response = Http::withHeaders([
                'Api-Key' => 'service.yoZD3QCLQAPUweIxRrKWV0eXCx69JGTfIqPpCEEy'
            ])->get("https://api.neshan.org/v5/reverse?lat={$this->lat}&lng={$this->lng}");
            if($response->status() == 200){

                $result = json_decode($response->body());
                $this->address_text = $result->formatted_address;
            }
        }
        elseif($prop == 'address_text'){
            $this->addresses = [];
            $response = Http::withHeaders([
                'Api-Key' => 'service.yoZD3QCLQAPUweIxRrKWV0eXCx69JGTfIqPpCEEy'
            ])->get("https://api.neshan.org/v1/search?term={$this->address_text}&lat=36.2966309&lng=59.6029849");
            if($response->status() == 200){
                $result = json_decode($response->body());
                $result = array_slice($result->items, 0, 10);
                foreach ($result as $item) {
                    $this->addresses[] = [
                        'lat' => $item->location->y,
                        'lng' => $item->location->x,
                        'address_text' => isset($item->address) ? $item->address : '',
                    ];
                }
            }
        }
    }

    public function update()
    {
        $full_address = $this->full_address ?? $this->address_text;
        if(isset($this->lat)){
            $this->address->lat = $this->lat;
        }
        if(isset($this->lng)){
            $this->address->lon = $this->lng;
        }
        $district = xDistrict([$this->address->lat,$this->address->lon]);
        $polygon = Polygon::where('region',$district)->first();
        if (!isset($polygon->city_id))
        {
            return sendToast(0,'شهری برای منطقه یافت نشد');
        }
        $city_id = $polygon->city_id  ;
        $this->address->city_id = $city_id;
        $this->address->address = $full_address;
        $this->address->save();
        $this->reset('lat','lng','address_text');
        $this->dispatch('reload-address');
    }
}
