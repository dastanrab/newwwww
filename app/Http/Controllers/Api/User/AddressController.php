<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\City;
use App\Models\Polygon;
use App\Models\PolygonDayHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $addresses = $user->addresses()->where('status',1)->get();
        $data = [];
        foreach ($addresses as $address){
            $data[] = [
                'id'      => $address->id,
                'title'   => $address->title,
                'address' => $address->address,
                'city'    => $address->city_id,
                'lat'     => $address->lat,
                'lng'     => $address->lon,
            ];
        }
        return sendJson('success','', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'title'      => 'nullable|min:2',
                'address'    => 'required|min:6',
                'lat'        => 'required',
                'lng'        => 'required',
                //'isFavorite' => 'required|in:true,false',
            ],
            [
                'title'      => 'عنوان آدرس را به درستی وارد نمایید',
                'address'    => 'آدرس را به درستی وارد نمایید',
                'lat'        => 'موقعیت lat ارسال نشده',
                'lng'        => 'موقعیت lng ارسال نشده',
                //'isFavorite' => 'نوع ذخیره شدن آدرس را به درستی وارد نکرده اید',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        $district = bazistDistrict([$request->lat,$request->lng]);
        $polygon = Polygon::where('region',$district)->first();
        if(!$polygon){
            return sendJson('error','شما خارج از محدوده هستید');
        }

        $user = auth()->user();
        $address = $user->addresses()->create([
            'city_id'  => $polygon->city_id,
            'title'    => $request->isFavorite == '1' ? $request->title ?? '' : '',
            'address'  => $request->address,
            'region'   => 0,
            'district' => 0,
            'lat'      => $request->lat,
            'lon'      => $request->lng,
            'status'   => $request->isFavorite == '1' ? 1 : 0,
        ]);
        if($address){
            $data = [
                'id'      => $address->id,
                'title'   => $address->title,
                'address' => $address->address,
                'city'    => $address->city_id,
                'lat'     => $address->lat,
                'lng'     => $address->lon,
            ];
            return sendJson('success', 'آدرس ذخیره شد', $data);
        }
        return sendJson('error', 'ثبت آدرس با اشکال روبرو شد');
    }

    public function destroy(Address $address)
    {
        $delete = $address->delete();
        if($delete){
            return sendJson('success','با موفقیت حذف شد');
        }
        return sendJson('error','حذف با اشکال روبرو شد');
    }
}
