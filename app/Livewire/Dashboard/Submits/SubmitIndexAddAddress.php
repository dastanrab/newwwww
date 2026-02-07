<?php

namespace App\Livewire\Dashboard\Submits;

use App\Models\Address;
use App\Models\City;
use App\Models\Polygon;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class SubmitIndexAddAddress extends Component
{
    public $user;
    public $lat;
    public $lng;
    public $address;
    public $address_text;
    public $addresses = [];
    public function render()
    {
        return view('livewire.dashboard.submits.submit-index-add-address');
    }

    public function updated($prop)
    {
        if($prop == 'lat'){
            $this->addresses= [];
            try {
                $response = Http::timeout(3)->withHeaders([
                    'Api-Key' => 'service.yoZD3QCLQAPUweIxRrKWV0eXCx69JGTfIqPpCEEy'
                ])->get("https://api.neshan.org/v5/reverse?lat={$this->lat}&lng={$this->lng}");
                if($response->status() == 200){
                    $result = json_decode($response->body());
                    $this->address = $result->formatted_address;
                }
            }catch (\Exception $exception){
                $this->addresses=[];
                $this->addresses[] = [
                    'lat' => $this->lat,
                    'lng' => $this->lng,
                    'address' => isset($item->address) ? $item->address : '',
                ];
            }

        }
        elseif($prop == 'address'){
            try {
                $this->addresses= [];
                $response = Http::timeout(3)->withHeaders([
                    'Api-Key' => 'service.yoZD3QCLQAPUweIxRrKWV0eXCx69JGTfIqPpCEEy'
                ])->get("https://api.neshan.org/v1/search?term={$this->address}&lat=36.2966309&lng=59.6029849");
                if($response->status() == 200){
                    $result = json_decode($response->body());
                    $result = array_slice($result->items, 0, 10);
                    foreach ($result as $item) {
                        $this->addresses[] = [
                            'lat' => $item->location->y,
                            'lng' => $item->location->x,
                            'address' => isset($item->address) ? $item->address : '',
                        ];
                    }
                }
            }catch (\Exception $exception ){
                $this->addresses=[];
                $this->addresses[] = [
                    'lat' => $this->lat,
                    'lng' => $this->lng,
                    'address' => $this->address,
                ];
            }

        }
    }

    public function add()
    {
        $this->validate([
            'lat'     => 'required',
            'lng'     => 'required|min:2',
            'address' => 'required|string|min:2'
        ],
        [
            'lat' => 'موقعیت به درستی وارد نشده',
            'lng' => 'موقعیت به درستی وارد نشده',
            'address' => 'آدرس را وارد نمایید',
        ]);


        $city_id = User::cityId();

        $addressValue = $this->address_text ?? $this->address;
        $district = xDistrict([$this->lat,$this->lng]);
        $polygon = Polygon::where('region',$district)->first();
        if (!isset($polygon->city_id))
        {
            return sendToast(1,'شهری برای منطقه یافت نشد');
        }
        $address = new Address;
        $address->city_id = $polygon->city_id;
        $address->title = '';
        $address->address = $addressValue;
        $address->region = 0;
        $address->status = 2;
        $address->district = 0;
        $address->lat = $this->lat;
        $address->lon = $this->lng;
        $address->user_id = $this->user->id;
        $address->save();
        $this->reset('lat','lng','address','address_text');
        $this->dispatch('reload-address');
    }

}
