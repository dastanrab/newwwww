<?php

namespace App\Livewire\Dashboard\Settings;

use Kavenegar;
use Kavenegar\KavenegarApi;
use Livewire\Attributes\Title;
use Livewire\Component;

class GeneralIndex extends Component
{

    public $breadCrumb = [['عمومی','d.settings.general']];
    #[Title('عمومی')]

    public $mobileSendApp;

    public function render()
    {
        $this->authorize('setting_general_index',GeneralIndex::class);
        return view('livewire.dashboard.settings.general-index');
    }

    public function sendApp()
    {

        try {
            //$k = new KavenegarApi(env('KAVENEGAR_API_KEY'));
            //$r = $k->Send('2000500666', $this->mobileSendApp, 'این پیامک تست می باشد');

            Kavenegar::VerifyLookup($this->mobileSendApp, 'بازیستی', '', '', "sendApp", "sms");
            $this->reset('mobileSendApp');
            return sendToast(1,'پیامک ارسال شد');
        }
        catch(\Kavenegar\Exceptions\ApiException $e){
            // در صورتی که خروجی وب سرویس 200 نباشد این خطا رخ می دهد
            return sendToast(0,$e->getMessage());
        }
        catch(\Kavenegar\Exceptions\HttpException $e){
            // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد
            return sendToast(0,$e->getMessage());
        }

    }

}
