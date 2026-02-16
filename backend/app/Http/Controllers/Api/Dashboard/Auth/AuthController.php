<?php

namespace App\Http\Controllers\Api\Dashboard\Auth;

use App\Http\Controllers\Api\User\SettingController;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessSms;
use App\Models\Otp;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public
        $type = 'mobile',
        $mobile,
        $password,
        $code;


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'mobile' => 'required|regex:/(09)[0-9]{9}/|digits:11|numeric|exists:users,mobile',
                'password' => 'required'
            ],[
                'mobile.exists' => 'شماره همراه وارد شده و یا رمز عبور اشتباه است.',
                'mobile' => 'شماره همراه را به درستی وارد نمایید.',
                'password' => 'گذرواژه را وارد نمایید.',
            ]
        );
        if($validator->fails()){
            return error_response($validator->errors()->first());
        }

        return $this->mobile($request->all());
    }
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'mobile' => 'required|regex:/(09)[0-9]{9}/|digits:11|numeric|exists:users,mobile',
                'code' => 'required'
            ],[
                'mobile.exists' => 'شماره همراه وارد شده و یا رمز عبور اشتباه است.',
                'mobile' => 'شماره همراه را به درستی وارد نمایید.',
                'code' => 'کد تایید را وارد نمایید.',
            ]
        );
        if($validator->fails()){
            return error_response($validator->errors()->first());
        }
            return $this->code($request->all());
    }

    public function mobile($data)
    {
        $mobile = $data['mobile'];

        $user = User::where('mobile',$mobile)->first();
        if(!$user->getRoles()->intersect(Role::AccessToDashboard())->count()){
            error_response('شما دسترسی ورود به پنل مدیریت را ندارید.',403);
        }
        elseif($user && Hash::check($data['password'], $user->password)){
            $expireTime = 3; // منقضی شدن کد تایید به دقیقه
            $expire = now()->addMinutes($expireTime)->toDateTimeString();
            $confirmation_code = mt_rand(100000, 999999);
            try {
                $receptor =  $mobile;
                $template =  "otp";
                $type =  "sms";
                $token =  $confirmation_code;
                $token2 =  "";
                $token3 =  "";
                if (App::environment('production') and $user->id != User::mayameyId()) {

                    ProcessSms::dispatch($receptor,$token,$token2,$token3,$template,$type);
                }
                else{
                    $confirmation_code = 123456;
                }
                if ($oldOtp = Otp::where('mobile', $mobile)->first()) {
                    $oldOtp->update(['code' => $confirmation_code, 'expired_at' => $expire]);
                } else {
                    Otp::create(['mobile' => $mobile, 'code' => $confirmation_code, 'expired_at' => $expire]);
                }
                return success_response('کد با موفقیت برای شما ارسال شد');

            } catch (Exeption $e) {
                return error_response('خطا در ارسال کد',500);
            }

        }
        else{
           return error_response('شماره و یا گذرواژه اشتباه است.',400);
        }
    }

    public function code($data)
    {
        $result = Otp::where([['mobile', $data['mobile'], ['code', $data['code']]]])->first();
        if ($result) {
                $user = User::where('mobile', $this->mobile)->first();
                if ($user) {
                    $token = $user->createToken('mobile')->plainTextToken;
                    $result->delete();
                    $user = User::find($user->id);
                    $setting = SettingController::data($user);
                    $data = [
                        "accessToken" => $token,
                        "settings" => $setting,
                    ];
                    return success_response($data);
                }

            else{
                return error_response('کد تایید اشتباه است.',400);
            }
        }
        else{
            return error_response("خطا در ورود به داشبورد",400);
        }
    }
}
