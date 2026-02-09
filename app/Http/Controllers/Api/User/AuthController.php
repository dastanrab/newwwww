<?php

namespace App\Http\Controllers\Api\User;

use App\Events\UserEvent;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessSms;
use App\Jobs\ProcessTopicFirebase;
use App\Models\ArchiveUser;
use App\Models\City;
use App\Models\Fava;
use App\Models\Otp;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Kavenegar;
use function Symfony\Component\Translation\t;

class AuthController extends Controller
{

    /**
     * ارسال کد یکبار مصرف
     *
     * این API برای ورود کاربران با شماره موبایل استفاده می‌شود.
     * در صورت معتبر بودن شماره، کد OTP ارسال می‌گردد.
     *
     * @group Auth
     *
     * @bodyParam mob string required شماره موبایل کاربر Example: 09123456789
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "کد یکبار مصرف به شماره شما ارسال شد"
     * }
     *
     * @response 400 {
     *   "status": "error",
     *   "message": "شماره همراه معتبر وارد نمایید"
     * }
     */
    public function login(Request $request)
    {
        if (!isMob($request->mob)) {
            return sendJson('error', 'شماره همراه معتبر وارد نمایید');
        }
        if (Otp::where('mobile', $request->mob)->where('expired_at', '>', now())->latest()->first()) {
            return sendJson('error', 'کد قبلا برای شما ارسال شده است اگر کد را دریافت نکرده اید لطفا بعد از چند دقیقه تلاش کنید');
        }

        $expireTime = 2; // منقضی شدن کد تایید به دقیقه
        $expire = now()->addMinutes($expireTime)->toDateTimeString();

        try {
       //     if (env('APP_ENV') == 'production' /*&& $request->mob != '09153105583'*/) {
//                $code = mt_rand(10000, 99999);
//                Kavenegar::VerifyLookup($request->mob, $code, '', '', "otp", "sms");
//            } else {
                $code = 12345;
//            }
            if ($oldOtp = Otp::where('mobile', $request->mob)->first()) {
                $oldOtp->update(['code' => $code, 'expired_at' => $expire]);
            } else {
                Otp::create(['mobile' => $request->mob, 'code' => $code, 'expired_at' => $expire]);
            }

            return sendJson('success', 'کد یکبار مصرف به شماره شما ارسال شد');

        } catch (Exeption $e) {
           // event(new UserEvent($e->getMessage(), false));
            return sendJson('success', 'هنگام ارسال کد یکبار مصرف خطایی پیش آمد لطفا دوباره امتحان کنید');
        }

    }

    /**
     * تایید کد یکبار مصرف
     *
     * با وارد کردن کد OTP، کاربر وارد سیستم شده
     * و توکن دسترسی دریافت می‌کند.
     *
     * @group Auth
     *
     * @bodyParam mob string required شماره موبایل Example: 09123456789
     * @bodyParam code string required کد تایید Example: 12345
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "با موفقیت وارد شدید",
     *   "data": {
     *     "accessToken": "token_here"
     *   }
     * }
     *
     * @response 400 {
     *   "status": "error",
     *   "message": "کد تایید اشتباه وارد شده است."
     * }
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'mob'     => 'required',
                'code'  => 'required|max:5',
            ],
            [
                'mob'    => 'شماره همراه وارد نمایید',
                'code' => 'کد وارد نمایید',
            ]);
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        $result = Otp::where([['mobile', $request->mob], ['code', $request->code]])->first();
        if (!$result) {
            return sendJson('error', 'کد تایید اشتباه وارد شده است.');
        } elseif (strtotime(now()->toDateTimeString()) > strtotime($result->expired_at)) {
            return sendJson('error', 'کد تایید منقضی شده است.');
        }

        $user = User::where('mobile', $request->mob)->first();
        if (!$user) {
            $user = User::create([
                'mobile' => $request->mob,
                'password' => Hash::make(strRandom(30)),
            ]);
            $user->roles()->sync([Role::where('name','user')->first()->id]);
            $user->wallets()->create(['wallet' => 0]);
        }

        if($user->legal == 1 && !isset($user->guild->id)){
            $user->guild_id = 10;
            $user->save();
        }

        $token = $user->createToken('mobile')->plainTextToken;
        $result->delete();
        $user = User::find($user->id);
        $setting = SettingController::data($user);
        $data = [
            "accessToken" => $token,
            "settings" => $setting,
        ];
        return sendJson('success', 'با موفقیت وارد شدید', $data);
    }

    /**
     * تکمیل ثبت نام کاربر
     *
     * این API بعد از ورود اولیه استفاده می‌شود
     * و اطلاعات کاربر را ثبت می‌کند.
     *
     * @group Auth
     * @authenticated
     *
     * @bodyParam userType string required نوع کاربر Example: citizen|guild
     * @bodyParam guildMarket integer شناسه صنف (برای guild)
     * @bodyParam guildTitle string نام کسب و کار
     * @bodyParam gender string required جنسیت Example: male|female
     * @bodyParam firstName string required نام Example: علی
     * @bodyParam lastName string required نام خانوادگی Example: رضایی
     * @bodyParam birthDate string تاریخ تولد
     * @bodyParam email string ایمیل
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "ثبت نام با موفقیت تکمیل شد"
     * }
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'userType'     => 'required|in:citizen,guild',
                'guildMarket'  => $request->userType == 'guild' ? 'required|exists:guilds,id' : 'nullable',
                'guildTitle'   => $request->userType == 'guild' ? 'required|min:3' : 'nullable',
                "gender"       => 'required|in:male,female',
                "firstName"    => 'required|min:2',
                "lastName"     => 'required|min:3',
                "birthDate"    => 'nullable',
                "email"        => 'nullable|email|unique:users,email',
            ],
            [
                'userType'    => 'نوع کاربری را وارد نمایید',
                'guildMarket' => 'نوع صنف را وارد نمایید',
                'guildTitle'  => 'لطفا نام کسب و کار را به درستی وارد نمایید',
                'gender'      => 'جنسیت را به درستی وارد نمایید',
                "firstName"   => 'نام خود را به درستی وارد نمایید',
                "lastName"    => 'نام خانوادگی خود را به درستی وارد نمایید',
                "email.email" => 'ایمیل را به درستی وارد نمایید',
            ]);
        $user_ref = null;
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        elseif($request->referralCode){
            $user_ref = User::find($request->referralCode - User::refCode());
            if(!$user_ref){
                return sendJson('error','کد معرف وارد شده اشتباه است');
            }
        }

        $city = City::where('name','mashhad')->first();
        $user = auth()->user();
        $legal = $request->userType == 'guild' ? 1 : 0;
        $notLegal = $request->userType == 'guild' ? 0 : 1;

        $user->update([
            'legal'       => $legal,
            'guild_id'    => $request->guildMarket,
            'guild_title' => $request->guildTitle,
            'name'        => $request->firstName,
            'lastname'    => $request->lastName,
            'gender'      => $request->gender == 'male' ? 1 : 2,
            'birthday'    => $request->birthDate ? toGregorian($request->birthDate,'/','-',false) : null,
            'email'       => $request->email,
            'city_id'     => $city->id
        ]);
        ArchiveUser::newArchive($city->id, $legal, $notLegal, 0, 1);
        if ($user_ref && $user->referral_code == null) {
            $user_ref->referrers()->create([
                'user_id' => $user_ref->id,
                'referrer_id' => $user->id
            ]);
            $user->referral_code = $request->referralCode;
            $user->save();
        }
        if (App::environment('production')) {
            ProcessSms::dispatch($user->mobile,$user->name,"","",'welcome','sms');
        }
        /*$user->fava_id = Fava::createUser([
            'userId' => $user->id,
            'mobile' => $user->mobile,
            'guildId' => $request->guildMarket,
            'isLegal' => $legal,
            'name' => $request->firstName,
            'lastname' => $request->lastName,
            'guildTitle' => $request->guildTitle,
            'cityId' => $city->id,
        ]);
        $user->save();*/
        $setting = SettingController::data();
        $userData = $setting['user'];
        return sendJson('success','ثبت نام با موفقیت تکمیل شد', $userData);

    }

    /**
     * ثبت نام با کد معرف
     *
     * این API برای ثبت نام اولیه کاربران با لینک معرف استفاده می‌شود.
     *
     * @group Auth
     *
     * @bodyParam mob string required شماره موبایل
     * @bodyParam refCode integer required کد معرف
     * @bodyParam token string required توکن امنیتی
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "ثبت نام اولیه با موفقیت انجام شد"
     * }
     */
    public function registerByRef(Request $request)
    {
        $user = User::where('mobile', $request->mob)->first();
        $userRef = User::find($request->refCode - User::refCode());
        if($request->token !== 'yRYXksfIBADJ8nlkVq3eAUjSVYPRPm7EjOytgG4iXq9Ki37ahdeBc4GCWpdVlMBnQaKb4tNh5nTaPEmNUxW6DYoLxiFvsu48HzHG') {
            return sendJson('error','خطایی پیش آمد');
        }
        elseif(!$userRef){
            return sendJson('error','کد معرف اشتباه است');
        }
        elseif ($user){
            return sendJson('error','این شماره قبلا ثبت نام کرده است');
        }
        else{
            $user = User::create([
                'city_id'=>1,
                'mobile' => $request->mob,
                'password' => Hash::make(strRandom(30)),
            ]);
            $user->roles()->sync([Role::where('name','user')->first()->id]);
            $user->wallets()->create(['wallet' => 0]);

            $userRef->referrers()->create(['user_id' => $userRef->id, 'referrer_id' => $user->id]);
            $user->referral_code = $request->refCode;
            $user->save();
            return sendJson('success','ثبت نام اولیه با موفقیت انجام شد');

        }
    }

    /**
     * ویرایش پروفایل کاربر
     *
     * اطلاعات پروفایل کاربر لاگین‌شده را بروزرسانی می‌کند.
     *
     * @group User
     * @authenticated
     *
     * @bodyParam firstName string required
     * @bodyParam lastName string required
     * @bodyParam email string ایمیل
     * @bodyParam gender string male|female
     * @bodyParam birthDate string تاریخ تولد
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "پروفایل ذخیره شد"
     * }
     */
    public function profile(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
                "firstName"    => 'required|min:2',
                "lastName"     => 'required|min:3',
                'email'        => 'nullable|email',
                'gender'       => 'nullable|in:male,female',
                "birthDate"    => 'nullable',
            ],
            [
                "firstName" => 'نام خود را به درستی وارد نمایید',
                "email"     => 'ایمیل معتبر وارد نمایید',
                "gender"    => 'جنسیت را به درستی وارد نمایید',
                "lastName"  => 'نام خانوادگی خود را به درستی وارد نمایید',
            ]);
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        $data = [
            'name'     => $request->firstName,
            'lastname' => $request->lastName,
            'birthday' => $request->birthDate ? toGregorian($request->birthDate,'/','-',false) : null,
            'email'    => $request->email,
        ];
        if($request->gender){
            $data['gender'] = $request->gender == 'male' ? 1 : 2;
        }
        $user->update($data);

        $user->save();
        $setting = SettingController::data();
        $userData = $setting['user'];
        return sendJson('success','پروفایل ذخیره شد', $userData);

    }

    /**
     * خروج از حساب کاربری
     *
     * توکن کاربر فعلی را حذف می‌کند.
     *
     * @group Auth
     * @authenticated
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "شما از نرم افزار خارج شدید"
     * }
     */
    public function logout(Request $request)

    {
        $user = auth()->user();
//        $user->firebases()->where('token',$request->fcmToken)->delete();
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
//        $appFirebase = $user->firebases()->where('platform','user-app')->first();
//        if($appFirebase){
//            $appFirebase->update(['token' => $request->token]);
//
//        }
//        else{
//            $user->firebases()->create([
//                'platform' => 'user-app',
//                'token' => $request->token,
//            ]);
//        }
//        $topics = ['all', 'allApp'];
//        if($user->gender) {
//            $topics[] = $user->gender == 1 ? 'male' : 'female';
//        }
//        if($user->city) {
//            $topics[] = $user->city->name;
//        }
       // ProcessTopicFirebase::dispatch($topics,$request->token);
        return sendJson('success','توکن fcm ذخیره شد');
    }
}
