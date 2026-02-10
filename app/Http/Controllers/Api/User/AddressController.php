<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\City;
use App\Models\Polygon;
use App\Models\PolygonDayHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * لیست آدرس‌های کاربر
     *
     * تمام آدرس‌های فعال (status=1) کاربر لاگین‌شده را برمی‌گرداند.
     *
     * @group Address
     * @authenticated
     *
     * @response 200 {
     * "status": "success",
     * "message": "",
     * "data": [
     *   {
     *     "id": 2,
     *     "title": "خونه",
     *     "address": "-",
     *     "city": 1,
     *     "lat": 36.1237,
     *     "lng": 88.0005
     *   }
     * ]
     *  }
     */
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

    /**
     * ثبت آدرس جدید
     *
     * آدرس کاربر را بر اساس موقعیت جغرافیایی ثبت می‌کند.
     * در صورت خارج بودن از محدوده، خطا برمی‌گرداند.
     *
     * @group Address
     * @authenticated
     *
     * @bodyParam title string عنوان آدرس Example: محل کار
     * @bodyParam address string required آدرس کامل Example: مشهد، احمدآباد، پلاک ۱۲
     * @bodyParam lat number required عرض جغرافیایی Example: 36.295
     * @bodyParam lng number required طول جغرافیایی Example: 59.607
     * @bodyParam isFavorite boolean ذخیره به عنوان آدرس اصلی Example: true
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "آدرس ذخیره شد",
     *   "data": {
     *     "id": 10,
     *     "title": "خانه",
     *     "address": "مشهد، احمدآباد",
     *     "city": 1,
     *     "lat": 36.295,
     *     "lng": 59.607
     *   }
     * }
     *
     * @response 400 {
     *   "status": "error",
     *   "message": "شما خارج از محدوده هستید"
     * }
     */
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
        $district = xDistrict([$request->lat,$request->lng]);
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

    /**
     * حذف آدرس
     *
     * آدرس انتخاب‌شده کاربر را حذف می‌کند.
     *
     * @group Address
     * @authenticated
     *
     * @urlParam address integer required شناسه آدرس Example: 12
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "با موفقیت حذف شد"
     * }
     *
     * @response 400 {
     *   "status": "error",
     *   "message": "حذف با اشکال روبرو شد"
     * }
     */


    public function search(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'address'    => 'required|min:6',
            ],
            [
                'address'    => 'آدرس را به درستی وارد نمایید',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        $addresses=[];
        $response = Http::timeout(8)->withHeaders([
            'Api-Key' => 'service.b28eded11be548d198058478e5296f16'
        ])->get("https://api.neshan.org/v1/search?term={$request->address}&lat=36.2966309&lng=59.6029849");
        if($response->status() == 200){
            $result = $response->json()->items ?? [];
            foreach ($result as $item) {
                $addresses[] = [
                    'lat' => $item->location->y,
                    'lng' => $item->location->x,
                    'address' => isset($item->address) ? $item->address : '',
                    'title'=> isset($item->title) ? $item->title : '',
                    'region'=> isset($item->region) ? $item->region : '',
                    'neighbourhood'=> isset($item->neighbourhood) ? $item->neighbourhood : '',
                ];
            }
        }
        return sendJson('success', 'لیست آدرس ها', $addresses);

    }
    public function destroy(Address $address)
    {
        if ($address->user_id != auth()->id()) {
            return sendJson('error','شما مجاز به حذف  نیستید');
        }
        $delete = $address->delete();
        if($delete){
            return sendJson('success','با موفقیت حذف شد');
        }
        return sendJson('error','حذف با اشکال روبرو شد');
    }
}
