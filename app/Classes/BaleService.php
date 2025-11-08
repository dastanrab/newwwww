<?php
namespace App\Classes;

use App\Models\Wallet;
use Illuminate\Support\Facades\Http;

class BaleService
{
protected $botToken;
protected $channelId;

public function __construct()
{
$this->botToken = '421751921:MeEykLh85hiDWAvpQM2DkY3fgYNhQtyhQAoWFwvo'; // توکن بات بله
$this->channelId = '5337227113';
}

public function sendMessage($message)
{
       $url = "https://tapi.bale.ai/bot{$this->botToken}/sendMessage";

    try {
        $request=Http::timeout(2)->post($url, [
            'chat_id' => $this->channelId,
            'text' => $this->formatException($message),
            'parse_mode' => 'Markdown'
        ]);
        if($request->json()['ok']){
            return true;
        }
        return false;
    }catch (\Exception $e){
        return false;
    }

}
 public function WalletLog($s,$e)
 {
     $filePath = storage_path('app/'.$s);
         $token='182541559:9RLotUnxp4Z7kX7qFHHwf6eEmjjkj6PtsNQAkOc8';
         $channelId='6094691689';
        $req= Http::withOptions(['timeout' => 70])->attach(
             'document',
             file_get_contents($filePath),
             $e
         )->post("https://tapi.bale.ai/bot{$token}/sendDocument", [
             'chat_id' => $channelId,
         ]);
        dd($req->body());
 }
    public function HooshLog($message,$data)
    {
        $token='182541559:9RLotUnxp4Z7kX7qFHHwf6eEmjjkj6PtsNQAkOc8';
        $url = "https://tapi.bale.ai/bot{$token}/sendMessage";
        try {
            $request=Http::timeout(3)->post($url, [
                'chat_id' => 5439387421,
                'text' => $this->HooshformatException($message,$data),
                'parse_mode' => 'Markdown'
            ]);
            if($request->json()['ok']){
                return true;
            }
            return false;
        }catch (\Exception $e){
            return false;
        }

 }
 public function HotLog($e)
 {
     $data=['class_name'=>get_class($e),'message'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine(),'trace'=>substr($e->getTraceAsString(), 0, 100)];
     $token='182541559:9RLotUnxp4Z7kX7qFHHwf6eEmjjkj6PtsNQAkOc8';
     $url = "https://tapi.bale.ai/bot{$token}/sendMessage";
     try {
         $request=Http::timeout(3)->post($url, [
             'chat_id' => 4753943538,
             'text' => $this->formatException($data),
             'parse_mode' => 'Markdown'
         ]);
         if($request->json()['ok']){
             return true;
         }
         return false;
     }catch (\Exception $e){
         return false;
     }
 }
    private  function formatException( $exception)
    {
        return "*🚨 Bazist Exception Report* \n\n" .
            "*📌 Exception:* `" . $exception['class_name'] . "`\n" .
            "*📜 Message:* `" . $exception['message'] . "`\n" .
            "*📂 File:* `" . $exception['file'] . "`\n" .
            "*📏 Line:* `" . $exception['line'] . "`\n\n" .
            "*🔍 Stack Trace:*\n```\n" . $exception['trace'] . "\n```";
    }
    private  function HooshformatException( $exception,$data)
    {
        return "*🚨 Hooshmap Exception Report* \n\n" .
            "*📌 Exception:* `" . @$exception['class_name'] . "`\n" .
            "*📜 Message:* `" . @$exception['message'] . "`\n" .
            "*📂 File:* `" . @$exception['file'] . "`\n" .
            "*📏 Line:* `" . @$exception['line'] . "`\n\n" .
            "*🔍 data:*\n```\n" . @$data . "\n```";
    }

    public function distanceLog($place,$data)
    {
        $token='640718644:hOMV2hID5jgobJUs2cAdyZLjrRTqN7Bw3N961EN4';
        $url = "https://tapi.bale.ai/bot{$token}/sendMessage";

        try {
            $request=Http::timeout(2)->post($url, [
                'chat_id' => 6185311410,
                'text' => $place .' شناسه درخواست شروع'.@$data['start'].'  شناسه درخواست پایان'.@$data['end'],
                'parse_mode' => 'Markdown'
            ]);
            if($request->json()['ok']){
                return true;
            }
            return false;
        }catch (\Exception $e){
            return false;
        }
    }
    public function raw_msg($data)
    {
        $token='640718644:hOMV2hID5jgobJUs2cAdyZLjrRTqN7Bw3N961EN4';
        $url = "https://tapi.bale.ai/bot{$token}/sendMessage";

        try {
            $request=Http::timeout(2)->post($url, [
                'chat_id' => 6185311410,
                'text' => json_encode($data),
                'parse_mode' => 'Markdown'
            ]);
            if($request->json()['ok']){
                return true;
            }
            return false;
        }catch (\Exception $e){
            return false;
        }
    }
    public function FavaError($part,$msg,$data)
    {
        $url = "https://tapi.bale.ai/bot{$this->botToken}/sendMessage";
        $msg="🚨 Fava Error Report
📌 Section : $part
    Data : {$data}
📜 Message: {$msg}";
        try {
            $request=Http::timeout(2)->post($url, [
                'chat_id' => 5510097107,
                'text' => $msg,
                'parse_mode' => 'Markdown'
            ]);
            if($request->json()['ok']){

                return true;
            }
            dd($request->json());
            return false;
        }catch (\Exception $e){
            return false;
        }

    }
    public function SubmitPayError($data)
    {
        $token='640718644:hOMV2hID5jgobJUs2cAdyZLjrRTqN7Bw3N961EN4';
        $url = "https://tapi.bale.ai/bot{$token}/sendMessage";

        try {
            $request=Http::timeout(2)->post($url, [
                'chat_id' => 6185311410,
                'text' => $this->formatException($data),
                'parse_mode' => 'Markdown'
            ]);
            if($request->json()['ok']){
                return true;
            }
            return false;
        }catch (\Exception $e){
            return false;
        }
    }
}
