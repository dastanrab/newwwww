<?php

namespace App\Livewire\Dashboard\Auth;

use App\Events\UserEvent;
use App\Jobs\ProcessSms;
use App\Models\ReferrerMobile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Login extends Component
{
    public
    $type = 'mobile',
    $mobile,
    $password,
    $code,
    $btnText = 'ارسال کد به شماره همراه';
    public function render()
    {
        dd('aa');
        return view('livewire.dashboard.auth.login');
    }
    public function login()
    {
        /*$ip = IP::where('ip', $request->ip())->first();
        if ($ip) {
            return response()->json(['errors' => ['mobile' => 'آی‌پی شما مسدود شده است.']], 403);
        }*/
        if($this->type == 'mobile'){
            $this->mobile();
        }
        elseif($this->type == 'code'){
            $this->code();
        }


    }

    public function mobile()
    {
        $data = $this->validate([
            'mobile' => 'required|regex:/(09)[0-9]{9}/|digits:11|numeric|exists:users,mobile',
            'password' => 'required'
        ],[
            'mobile.exists' => 'شماره همراه وارد شده و یا رمز عبور اشتباه است.',
            'mobile' => 'شماره همراه را به درستی وارد نمایید.',
            'password' => 'گذرواژه را وارد نمایید.',
        ]);

        $mobile = $data['mobile'];

        $user = User::where('mobile',$mobile)->first();
        if(!$user->getRoles()->intersect(Role::AccessToDashboard())->count()){
            sendToast(0,'شما دسترسی ورود به پنل مدیریت را ندارید.');
        }
        elseif($user && Hash::check($data['password'], $user->password)){
            $this->type = 'code';
            $confirmation_code = mt_rand(100000, 999999);
            try {
                $receptor =  $mobile;
                $template =  "otp";
                $type =  "sms";
                $token =  $confirmation_code;
                $token2 =  "";
                $token3 =  "";
//                if (App::environment('production') and $user->id != User::mayameyId()) {
                    //$k = new KavenegarApi(env('KAVENEGAR_API_KEY'));
                    //$k->VerifyLookup($receptor,$token,$token2,$token3,$template,$type);
//                    ProcessSms::dispatch($receptor,$token,$token2,$token3,$template,$type);
//                }
//               else{
                    $confirmation_code = 123456;
//                }
                session()->put('code', $confirmation_code);
                event(new UserEvent('user sent sms for login'));

                $this->type = 'code';
                $this->btnText = 'ورود به داشبورد';
            } catch (Exeption $e) {
                echo 'Error UltraFastSend: '.$e->getMessage();
                event(new UserEvent($e->getMessage(), false));
                sendToast(0,'ارسال کد تایید با مشکل روبرو شد لطفا مشکل را با پشتیبان درمیان بگذارید.');
            }

        }
        else{
            sendToast(0,'شماره و یا گذرواژه اشتباه است.');
        }
    }

    public function code()
    {
        if (session()->exists('code')) {
            if ($this->code == session('code')) {
                $this->btnText = 'درحال ورود به داشبورد...';
                $user = User::where('mobile', $this->mobile)->first();
                if ($user) {
                    event(new UserEvent('کد تایید برای ورود به داشبورد توسط کاربر درست وارد شد.'));
                    $credentials = ['mobile' => $this->mobile, 'password' => $this->password];
                    if (Auth::attempt($credentials, true)) {
                        session()->forget(['code']);
                    }
                    setcookie('city_id',$user->city_id,time() + ((86400 * 365))*10, "/"); // 86400 = 1 day
                    return $this->redirect(route('d.home'));
                }
            }
            else{
                event(new UserEvent("هنگام ورود به داشبورد کد تایید اشتباه وارد شد.", false));
                sendToast(0,'کد تایید اشتباه است.');
            }
        }
        else{
            event(new UserEvent("خطا در ورود به داشبورد", false));
            return $this->redirect(route('d.login'));
        }
    }
}
