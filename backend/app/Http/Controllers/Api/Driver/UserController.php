<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\User\SettingController;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\ArchiveUser;
use App\Models\City;
use App\Models\Driver;
use App\Models\Polygon;
use App\Models\Role;
use App\Models\Submit;
use App\Models\SubmitTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function userCheck(Request $request)
    {
        if (!isMob($request->mob)) {
            return sendJson('error', 'شماره همراه معتبر وارد نمایید');
        }
        $query = User::where('mobile',$request->mob)->first();
        $userExists = false;
        if($query){
            $userExists = true;
        }
        return sendJson('success','', ['isRegistered' => $userExists]);
    }

    public function register(Request $request)
    {
        // mob firstname lastname legal guild_id guild_title gender

        $validator = Validator::make($request->all(),
        [
            'mob'          => 'required|unique:users,mobile',
            'userType'     => 'required|in:citizen,guild',
            'guildMarket'  => $request->userType == 'guild' ? 'required|exists:guilds,id' : 'nullable',
            'guildTitle'   => $request->userType == 'guild' ? 'required|min:3' : 'nullable',
            "gender"       => 'required|in:male,female',
            "firstName"    => 'required|min:2',
            "lastName"     => 'required|min:3',
        ],
        [
            'mob'         => 'این شماره همراه قبلا ثبت شده است',
            'userType'    => 'نوع کاربری را وارد نمایید',
            'guildMarket' => 'نوع صنف را وارد نمایید',
            'guildTitle'  => 'لطفا نام کسب و کار را به درستی وارد نمایید',
            'gender'      => 'جنسیت را به درستی وارد نمایید',
            "firstName"   => 'نام خود را به درستی وارد نمایید',
            "lastName"    => 'نام خانوادگی خود را به درستی وارد نمایید',
        ]);
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        $driver = auth()->user();
        $city = City::where('name','mashhad')->first();
        $legal = $request->userType == 'guild' ? 1 : 0;
        $notLegal = $request->userType == 'guild' ? 0 : 1;
        $user = User::create([
            'mobile'        => $request->mob,
            'password'      => Hash::make(strRandom(30)),
            'legal'         => $legal,
            'guild_id'      => $request->guildMarket,
            'guild_title'   => $request->guildTitle,
            'name'          => $request->firstName,
            'lastname'      => $request->lastName,
            'gender'        => $request->gender == 'male' ? 1 : 2,
            'referral_code' => $driver->referral(),
            'city_id'       => $city->id,
        ]);
        $user->roles()->sync([Role::where('name','user')->first()->id]);
        $user->wallets()->create(['wallet' => 0]);
        ArchiveUser::newArchive($city->id, $legal, $notLegal, 0, 1);
        $driver->referrers()->create([
            'user_id' => $driver->id,
            'referrer_id' => $user->id
        ]);

        return sendJson('success','ثبت نام با موفقیت انجام شد');
    }

    public function storeAddress(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'mob'        => 'required|exists:users,mobile',
                'address'    => 'required|min:6',
                'lat'        => 'required',
                'lng'        => 'required',
            ],
            [
                'mob'        => 'شماره همراه در سیستم وجود ندارد',
                'address'    => 'آدرس را به درستی وارد نمایید',
                'lat'        => 'موقعیت lat ارسال نشده',
                'lng'        => 'موقعیت lng ارسال نشده',
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

        $user = User::where('mobile',$request->mob)->first();
        $address = $user->addresses()->create([
            'city_id'  => $user->city->id,
            'title'    => '',
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
                'address' => $address->address,
                'city'    => $address->city_id,
                'lat'     => $address->lat,
                'lng'     => $address->lon,
            ];
            return sendJson('success', 'آدرس ذخیره شد', $data);
        }
        return sendJson('error', 'ثبت آدرس با اشکال روبرو شد');
    }

    public function scheduling(Request $request)
    {
        $user = User::where('mobile',$request->mob)->first();
        $validator = Validator::make($request->all(),
            [
                'mob'       => 'required|exists:users,mobile',
                'addressId' => 'required|exists:addresses,id,user_id,'.$user->id,
            ],
            [
                'mob'                => 'شماره همراه در سیستم وجود ندارد',
                'addressId.required' => 'لطفا آدرس را ارسال نمایید',
                'addressId.exists'   => 'آدرسی برای شما با این مشخصات ثبت نشده است.',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }

        $address = Address::find($request->addressId);
        $data = Submit::schedule($user,$address);

        return sendJson('success', '', $data);
    }

    public function storeRequest(Request $request)
    {
        $user = User::where('mobile',$request->mob)->first();
        $driver = auth()->user();
        $validator = Validator::make($request->all(),
            [
                'mob'             => 'required|exists:users,mobile',
                'addressId'       => 'required|exists:addresses,id,user_id,'.$user->id,
                'scheduling.day'  => $request->scheduling == 'immediate' ? 'nullable' : 'required',
                'scheduling.hour' => $request->scheduling == 'immediate' ? 'nullable' : 'required',
            ],
            [
                'mob'                => 'شماره همراه در سیستم وجود ندارد',
                'addressId.required' => 'لطفا آدرس را ارسال نمایید',
                'scheduling.day'     => 'لطفا روز جمع آوری را انتخاب کنید',
                'scheduling.hour'    => 'لطفا ساعت جمع آوری را انتخاب کنید',
            ]
        );
        $address = Address::find($request->addressId);
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        elseif(Submit::where('user_id', $user->id)->where('status', 1)->first()){
            return sendJson('error','این کاربر یک درخواست فعال دارد');
        }
        elseif($request->scheduling == 'immediate' && !Submit::immediateValidate(verta()->format('G'))){
            return sendJson('error','درحال حاضر ثبت درخواست فوری در این بازه غیرفعال می باشد.');
        }
        elseif(isset($request->scheduling['day']) && !Submit::scheduleValidation($request->addressId,$request->scheduling['day'],$request->scheduling['hour'])){
            return sendJson('error','ثبت درخواست در زمان انتخاب شده امکان پذیر نمی باشد.');
        }
        elseif (!isLocationInsidePolygon($driver->id,[$address->lat,$address->lon])){
            return sendJson('error','این درخواست خارج از منطقه شما می باشد');
        }
        $submit = Submit::add($driver->id,$user,$request);
        if($submit) {
            $saveDriver = Driver::add($driver, $submit);
            return sendJson('success', 'درخواست شما با موفقیت ثبت شد', $user->currentSubmit());
        }
        else{
            return sendJson('error','اشکالی پیش آمد');
        }
    }
}
