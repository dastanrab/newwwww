<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function apCallBack()
    {

        $logo = asset('/assets/img/bazist-logo-2.png');
        $textBtn = "برگرد به آنیروب";
        $description = '';
        $link = '';
        $icon = '';
        $alertClass = 'alert-info';
        if(isset($_GET['ReqResult']) && $_GET['ReqResult'] == 'AllowedAccessWallet') {
            $description = 'دسترسی آنیروب به آسان پرداخت (آپ) فعال شد.';
            $alertClass = 'success';
            $icon = 'bx bx-log-in-circle';
            $jsonMessage = 'مجوز آپ با موفقیت اعمال شد';
        }
        elseif(isset($_GET['ReqResult']) && $_GET['ReqResult'] == 'NotAllowedAccessWallet') {
            $description = 'دسترسی آنیروب به آسان پرداخت (آپ) فعال نشد.';
            $alertClass = 'danger';
            $icon = 'bx bx-log-in-circle';
            $jsonMessage = 'مجوز آپ توسط شما رد شد';
        }

        $json = urlencode(json_encode([
            'type' => 'aap',
            'data' => [
                'status' =>  'success',
                'message' => $jsonMessage
            ]
        ]));

        if(isset($_GET['platform'])){
            if($_GET['platform'] == 'android'){
                $link = "bazist://app?payload=$json/#/wallet";
            }
            elseif($_GET['platform'] == 'pwa'){
                $link = "https://app.bazistco.com/#/wallet/?payload=$json";
            }
        }
        return view('pages.asanpardakht.callback', compact('logo','link','textBtn', 'description','alertClass','icon'));
    }
}
