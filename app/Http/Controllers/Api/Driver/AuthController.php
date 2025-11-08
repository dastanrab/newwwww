<?php

namespace App\Http\Controllers\Api\Driver;

use App\Events\UserEvent;
use App\Http\Controllers\Api\User\Exeption;
use App\Http\Controllers\Controller;
use App\Models\ArchiveUser;
use App\Models\City;
use App\Models\Fava;
use App\Models\Otp;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Kavenegar;
use function Symfony\Component\Translation\t;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (!isMob($request->mob)) {
            return sendJson('error', 'شماره همراه معتبر وارد نمایید');
        }
        elseif (Otp::where('mobile', $request->mob)->where('expired_at', '>', now())->latest()->first()) {
            return sendJson('error', 'کد قبلا برای شما ارسال شده است اگر کد را دریافت نکرده اید لطفا بعد از چند دقیقه تلاش کنید');
        }
        $driver = User::with('roles')->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })->where('mobile',$request->mob)->first();
        if(!$driver){
            return sendJson('error','کاربری با این مشخصات یافت نشد');
        }
        elseif($driver->car->is_active == 0){
            return sendJson('error','شما دسترسی به اپ راننده ندارید');
        }

        $expireTime = 3; // منقضی شدن کد تایید به دقیقه
        $expire = now()->addMinutes($expireTime)->toDateTimeString();

        try {
            if (true) {
                $code = 12345;
            } else {
                $code = mt_rand(10000, 99999);
                Kavenegar::VerifyLookup($request->mob, $code, '', '', "otp", "sms");
            }
            if ($oldOtp = Otp::where('mobile', $request->mob)->first()) {
                $oldOtp->update(['code' => $code, 'expired_at' => $expire]);
            } else {
                Otp::create(['mobile' => $request->mob, 'code' => $code, 'expired_at' => $expire]);
            }

            return sendJson('success', 'کد یکبار مصرف به شماره شما ارسال شد');

        } catch (Exeption $e) {
            event(new UserEvent($e->getMessage(), false));
            return sendJson('success', 'هنگام ارسال کد یکبار مصرف خطایی پیش آمد لطفا دوباره امتحان کنید');
        }

    }

    public function verify(Request $request)
    {
        $result = Otp::where([['mobile', $request->mob], ['code', $request->code]])->first();
        if (!$result) {
            return sendJson('error', 'کد تایید اشتباه وارد شده است.');
        } elseif (strtotime(now()->toDateTimeString()) > strtotime($result->expired_at)) {
            return sendJson('error', 'کد تایید منقضی شده است.');
        }
        $user = User::where('mobile', $request->mob)->first();
        $token = $user->createToken('mobile')->plainTextToken;
        $result->delete();
        $setting = SettingController::data($user);
        $data = [
            "accessToken" => $token,
            "settings" => $setting,
        ];
        return sendJson('success', 'با موفقیت وارد شدید', $data);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        $user->firebases()->where('token',$request->fcmToken)->delete();
        $user->currentAccessToken()->delete();
        return sendJson('success','شما از نرم افزار خارج شدید');
    }

    public function fcm(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
                "token"    => 'required',
            ],
            [
                "token"   => 'توکن fcm دریافت نشد',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        $appFirebase = $user->firebases()->where('platform','driver-app')->first();
        if($appFirebase){
            $appFirebase->update(['token' => $request->token]);
        }
        else{
            $user->firebases()->create([
                'platform' => 'driver-app',
                'token' => $request->token,
            ]);
        }
        return sendJson('success','توکن fcm ذخیره شد');
    }

}
