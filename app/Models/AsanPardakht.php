<?php

namespace App\Models;

use App\Events\ActivityEvent;
use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class AsanPardakht extends Model
{
    use HasFactory;
    use RecordsActivity;
    // type: model name, type_id: model id, host_id: const, rrn: ref code
    protected $fillable = ['fava_id', 'user_id ', 'type', 'type_id', 'host_id',
        'host_tran_id', 'host_req_time', 'host_opcode', 'status_code',
        'amount'/*Rial*/, 'wallet_balance', 'settle_token', 'rrn', 'status_message', 'details'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public static function signature($data)
    {
        $pkeyid = openssl_pkey_get_private(env('BAZIST_PRIVATE_KEY'));
        openssl_sign($data, $signature, $pkeyid, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    public static function permission($mobile)
    {
        $data = "{\"hi\":2408,\"htran\":" . hexdec(uniqid()) . ",\"htime\":" . time() . ",\"hop\":312,\"hkey\":" . env('AP_APIKEY') . ",\"mo\":" . $mobile . ",\"walet\":5,\"caurl\":\"https://la.bazistco.com/asanpardakht/callback\"}";
        try {
            $response = Http::post(env('AP_BASE_URL'), [
                "hreq" => $data,
                "hsign" => "1#1#" . self::signature($data),
                "ver" => "1.0.0"
            ]);
            return $response->json();

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'AsanPardakht', false));
        }
    }

    public static function submitPermission($mobile)
    {
        $ap_apikey = env('AP_APIKEY');
        $data = "{\"hi\":2408,\"htran\":" . hexdec(uniqid()) . ",\"htime\":" . time() . ",\"hop\":333,\"hkey\":\"$ap_apikey\",\"mo\":\"$mobile\",\"walet\":5,\"apf\":true}";
        try {
            $response = Http::post(env('AP_BASE_URL'), [
                "hreq" => $data,
                "hsign" => "1#1#" . self::signature($data),
                "ver" => "1.0.0"
            ]);
            return $response->json();

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'AsanPardakht', false));
        }
    }

    public static function resetPermission($mobile)
    {
        $ap_apikey = env('AP_APIKEY');
        $data = "{\"hi\":2408,\"htran\":" . hexdec(uniqid()) . ",\"htime\":" . time() . ",\"hop\":333,\"hkey\":\"$ap_apikey\",\"mo\":\"$mobile\",\"walet\":5,\"apf\":false}";
        try {
            $response = Http::post(env('AP_BASE_URL'), [
                "hreq" => $data,
                "hsign" => "1#1#" . self::signature($data),
                "ver" => "1.0.0"
            ]);
            return $response->json();

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'AsanPardakht', false));
        }
    }

    public static function balance($mobile)
    {
        $ap_apikey = env('AP_APIKEY');
        $data = "{\"hi\":2408,\"htran\":" . hexdec(uniqid()) . ",\"htime\":" . time() . ",\"hop\":310,\"hkey\":\"$ap_apikey\",\"mo\":\"$mobile\",\"walet\":5,\"caurl\":\"https://bazistco.com/wallets/back\"}";

        try {
            $response = Http::post(env('AP_BASE_URL'), [
                "hreq" => $data,
                "hsign" => "1#1#" . self::signature($data),
                "ver" => "1.0.0"
            ]);
            if ($response->ok()) {
                $card = $response->json();
                if ($card) {
                    $hresp = json_decode($card['hresp']);
                    if ($hresp->st == 0) {
                        return $hresp->wball / 10;
                    } else {
                        return -1;
                    }
                }
            } else {
                return false;
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'AsanPardakht', false));
        }
    }

    public static function check($mobile,$callback = null)
    {
        if(!$callback){
            $callback = 'https://la.bazistco.com/asanpardakht/callback';
        }
        $ap_apikey = env('AP_APIKEY');
        $data = "{\"hi\":2408,\"htran\":" . hexdec(uniqid()) . ",\"htime\":" . time() . ",\"hop\":310,\"hkey\":\"$ap_apikey\",\"mo\":\"$mobile\",\"walet\":5,\"caurl\":\"$callback\"}";
        try {
            $response = Http::post(env('AP_BASE_URL'), [
                "hreq" => $data,
                "hsign" => "1#1#" . self::signature($data),
                "ver" => "1.0.0"
            ]);
            if ($response->ok()) {
                $card = $response->json();
                if ($card) {
                    $hresp = json_decode($card['hresp']);
                    if ($hresp->st == 0) {
                        return ['status' => 1, 'data' => $hresp->wball / 10];
                    } else {
                        return ['status' => 2, 'data' => $hresp->addData->ipgURL];
                    }
                }

            } else {
                return ['status' => 0, 'data' => 'اتصال به آپ با اشکال روبرو شد'];
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'AsanPardakht', false));
        }

    }


    public static function decrease($mobile, $price)
    { // کاهش اعتبار کیف پول آپ کاربر
        $ap_apikey = env('AP_APIKEY');
        $data = "{\"hi\":2408,\"htran\":" . hexdec(uniqid()) . ",\"htime\":" . time() . ",\"hop\":243,\"hkey\":\"$ap_apikey\",\"mo\":\"$mobile\",\"ao\":\"$price\",\"walet\":5,\"caurl\":\"https://bazistco.com/wallets/back\"}";

        try {
            $response = Http::post(env('AP_BASE_URL'), [
                "hreq" => $data,
                "hsign" => "1#1#" . self::signature($data),
                "ver" => "1.0.0"
            ]);
            if ($response->ok()) {
                return $response->json();
            } else {
                return false;
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'AsanPardakht', false));
        }
    }

    public static function sharj($mobile, $price)
    {
        $ap_apikey = env('AP_APIKEY');
        $data = "{\"hi\":2408,\"htran\":" . hexdec(uniqid()) . ",\"htime\":" . time() . ",\"hop\":313,\"hkey\":\"$ap_apikey\",\"mo\":\"$mobile\",\"ao\":" . $price . ",\"walet\":5,\"caurl\":\"https://bazistco.com/wallets/back\"}";

        try {
            $response = Http::post(env('AP_BASE_URL'), [
                "hreq" => $data,
                "hsign" => "1#1#" . self::signature($data),
                "ver" => "1.0.0"
            ]);
            if ($response->ok()) {
                return $response->json();
            } else {
                return false;
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'AsanPardakht', false));
        }
    }

    public static function increase($mobile, $price)
    {// افزایش اعتبار کیف پول آپ کاربر
        self::submitPermission($mobile);

        $ap_apikey = env('AP_APIKEY');
        $data = "{\"hi\":2408,\"htran\":" . hexdec(uniqid()) . ",\"htime\":" . time() . ",\"hop\":244,\"hkey\":\"$ap_apikey\",\"mo\":\"$mobile\",\"ao\":" . $price . ",\"walet\":5,\"caurl\":\"https://bazistco.com\"}";
        try {
            $response = Http::post(env('AP_BASE_URL'), [
                "hreq" => $data,
                "hsign" => "1#1#" . self::signature($data),
                "ver" => "1.0.0"
            ]);
            if ($response->ok()) {
                return $response->json();
            } else {
                return false;
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'AsanPardakht', false));
        }
    }

    public static function verify($htran, $htime, $price, $stkn)
    {
        $ap_apikey = env('AP_APIKEY');
        $data = "{\"hi\":2408,\"htran\":" . $htran . ",\"htime\":" . $htime . ",\"hop\":2001,\"hkey\":\"$ap_apikey\",\"ao\":" . $price . ",\"stime\":" . time() . ",\"stkn\":\"$stkn\"}";

        try {
            $response = Http::post(env('AP_BASE_URL'), [
                "hreq" => $data,
                "hsign" => "1#1#" . self::signature($data),
                "ver" => "1.0.0"
            ]);
            if ($response->ok()) {
                return $response->json();
            } else {
                return false;
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'AsanPardakht', false));
        }
    }

    public static function reverse($htran, $htime, $price, $stkn)
    {
        $ap_apikey = env('AP_APIKEY');
        $data = "{\"hi\":2408,\"htran\":" . $htran . ",\"htime\":" . $htime . ",\"hop\":2003,\"hkey\":\"$ap_apikey\",\"ao\":" . $price . ",\"stime\":" . time() . ",\"stkn\":\"$stkn\"}";

        try {
            $response = Http::post(env('AP_BASE_URL'), [
                "hreq" => $data,
                "hsign" => "1#1#" . self::signature($data),
                "ver" => "1.0.0"
            ]);
            if ($response->ok()) {
                return $response->json();
            } else {
                return false;
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'AsanPardakht', false));
        }
    }

    public static function addInaxChargeRecord($userid,$cityId,$inaxId,$hresp)
    {
        $buy_inax = new AsanPardakht;
        $buy_inax->user_id = $userid;
        $buy_inax->type = 'sharj_mobile';
        $buy_inax->type_id = $inaxId;
        $buy_inax->host_id = $hresp->hi;
        $buy_inax->host_tran_id = $hresp->htran;
        $buy_inax->host_req_time = $hresp->htime;
        $buy_inax->host_opcode = $hresp->hop;
        $buy_inax->status_code = $hresp->st;
        $buy_inax->amount = $hresp->ao;
        $buy_inax->wallet_balance = $hresp->wball;
        $buy_inax->settle_token = $hresp->stkn;
        $buy_inax->rrn = $hresp->rrn;
        $buy_inax->status_message = $hresp->stm;
        $buy_inax->details = 'شارژ موبایل';
        $buy_inax->city_id = $cityId;
        $buy_inax->method = 'واریز';
        $buy_inax->save();
        return $buy_inax;
    }


    public static function addInaxInternetRecord($userid,$cityId,$inaxId,$hresp){
        $buy_inax = new AsanPardakht;
        $buy_inax->user_id = $userid;
        $buy_inax->type = 'sharj_internet';
        $buy_inax->type_id = $inaxId;
        $buy_inax->host_id = $hresp->hi;
        $buy_inax->host_tran_id = $hresp->htran;
        $buy_inax->host_req_time = $hresp->htime;
        $buy_inax->host_opcode = $hresp->hop;
        $buy_inax->status_code = $hresp->st;
        $buy_inax->amount = $hresp->ao;
        $buy_inax->wallet_balance = $hresp->wball;
        $buy_inax->settle_token = $hresp->stkn;
        $buy_inax->rrn = $hresp->rrn;
        $buy_inax->status_message = $hresp->stm;
        $buy_inax->details = 'شارژ اینترنت';
        $buy_inax->city_id = $cityId;
        $buy_inax->method = 'واریز';
        $buy_inax->save();
        return $buy_inax;
    }

    public static function addCharityWithdrawRecord($hresp,$charityName,$cityId)
    {
        $buy_asan = new AsanPardakht;
        $buy_asan->user_id = auth()->id();
        $buy_asan->type = 'charity_out';
        $buy_asan->type_id = null;
        $buy_asan->host_id = $hresp->hi;
        $buy_asan->host_tran_id = $hresp->htran;
        $buy_asan->host_req_time = $hresp->htime;
        $buy_asan->host_opcode = $hresp->hop;
        $buy_asan->status_code = $hresp->st;
        $buy_asan->amount = $hresp->ao;
        $buy_asan->wallet_balance = $hresp->wball;
        $buy_asan->settle_token = $hresp->stkn;
        $buy_asan->rrn = $hresp->rrn;
        $buy_asan->status_message = $hresp->stm;
        $buy_asan->details = 'برداشت برای ' . $charityName;
        $buy_asan->city_id = $cityId;
        $buy_asan->save();
        return $buy_asan;
    }
    public static function addCharityDepositRecord($hresp,$charityName,$cityId)
    {
        $charity_asan = new AsanPardakht;
        $charity_asan->user_id = auth()->id();
        $charity_asan->type = 'charity_in';
        $charity_asan->type_id = null;
        $charity_asan->host_id = $hresp->hi;
        $charity_asan->host_tran_id = $hresp->htran;
        $charity_asan->host_req_time = $hresp->htime;
        $charity_asan->host_opcode = $hresp->hop;
        $charity_asan->status_code = $hresp->st;
        $charity_asan->amount = $hresp->ao;
        $charity_asan->wallet_balance = $hresp->wball;
        $charity_asan->settle_token = $hresp->stkn;
        $charity_asan->rrn = $hresp->rrn;
        $charity_asan->status_message = $hresp->stm;
        $charity_asan->details = 'پرداخت به ' . $charityName;
        $charity_asan->city_id = $cityId;
        $charity_asan->save();
        return $charity_asan;
    }

    public static function withdrawRecord($userId,$withdrawHresp,$cityId)
    {
        $ap_user = new AsanPardakht();
        $ap_user->user_id = $userId;
        $ap_user->type = 'to_aap';
        $ap_user->type_id = null;
        $ap_user->host_id = $withdrawHresp->hi;
        $ap_user->host_tran_id = $withdrawHresp->htran;
        $ap_user->host_req_time = $withdrawHresp->htime;
        $ap_user->host_opcode = $withdrawHresp->hop;
        $ap_user->status_code = $withdrawHresp->st;
        $ap_user->amount = $withdrawHresp->ao;
        $ap_user->wallet_balance = $withdrawHresp->wball;
        $ap_user->settle_token = $withdrawHresp->stkn;
        $ap_user->rrn = $withdrawHresp->rrn;
        $ap_user->status_message = $withdrawHresp->stm;
        $ap_user->details = 'انتقال به کیف پول آپ';
        $ap_user->city_id = $cityId;
        $ap_user->method = 'برداشت';
        $ap_user->save();
        return $ap_user;
    }
    public static function rawRecord($userId,$amount,$cityId)
    {
        $ap_user = new AsanPardakht();
        $ap_user->user_id = $userId;
        $ap_user->type = 'to_aap';
        $ap_user->type_id = null;
        $ap_user->host_id = 0;
        $ap_user->host_tran_id = '-';
        $ap_user->host_req_time = '-';
        $ap_user->host_opcode = 0;
        $ap_user->status_code = 0;
        $ap_user->amount = $amount;
        $ap_user->wallet_balance = 0;
        $ap_user->settle_token = '-';
        $ap_user->rrn = '-';
        $ap_user->status_message = '-';
        $ap_user->details = 'انتقال به کیف پول آپ';
        $ap_user->city_id = $cityId;
        $ap_user->method = 'برداشت';
        $ap_user->save();
        return $ap_user;
    }
    public static function newWithdrawRecord($aap_id,$userId,$withdrawHresp,$cityId)
    {
        $ap_user =AsanPardakht::query()->where('id',$aap_id)->first();
        $ap_user->user_id = $userId;
        $ap_user->type = 'to_aap';
        $ap_user->type_id = null;
        $ap_user->host_id = $withdrawHresp->hi;
        $ap_user->host_tran_id = $withdrawHresp->htran;
        $ap_user->host_req_time = $withdrawHresp->htime;
        $ap_user->host_opcode = $withdrawHresp->hop;
        $ap_user->status_code = $withdrawHresp->st;
        $ap_user->amount = $withdrawHresp->ao;
        $ap_user->wallet_balance = $withdrawHresp->wball;
        $ap_user->settle_token = $withdrawHresp->stkn;
        $ap_user->rrn = $withdrawHresp->rrn;
        $ap_user->status_message = $withdrawHresp->stm;
        $ap_user->details = 'انتقال به کیف پول آپ';
        $ap_user->city_id = $cityId;
        $ap_user->method = 'برداشت';
        $ap_user->save();
        return $ap_user;
    }


    public static function submitUserRecord($userId,$driver,$userHresp)
    {
        $ap_user = new AsanPardakht;
        $ap_user->user_id = $userId;
        $ap_user->type = 'submit_user';
        $ap_user->type_id = $driver->id;
        $ap_user->host_id = $userHresp->hi;
        $ap_user->host_tran_id = $userHresp->htran;
        $ap_user->host_req_time = $userHresp->htime;
        $ap_user->host_opcode = $userHresp->hop;
        $ap_user->status_code = $userHresp->st;
        $ap_user->amount = $userHresp->ao;
        $ap_user->wallet_balance = $userHresp->wball;
        $ap_user->settle_token = $userHresp->stkn;
        $ap_user->rrn = $userHresp->rrn;
        $ap_user->status_message = $userHresp->stm;
        $ap_user->details = 'سهم شهروند';
        $ap_user->city_id = $driver->city_id;
        $ap_user->method = 'برداشت';
        $ap_user->save();
        return $ap_user;
    }

    public static function firstSubmitUserRecord($userId,$driver,$userHresp)
    {
        $ap_user_reward = new AsanPardakht;
        $ap_user_reward->user_id = $userId;
        $ap_user_reward->type = 'first_submit_user';
        $ap_user_reward->type_id = $driver->id;
        $ap_user_reward->host_id = $userHresp->hi;
        $ap_user_reward->host_tran_id = $userHresp->htran;
        $ap_user_reward->host_req_time = $userHresp->htime;
        $ap_user_reward->host_opcode = $userHresp->hop;
        $ap_user_reward->status_code = $userHresp->st;
        $ap_user_reward->amount = $userHresp->ao;
        $ap_user_reward->wallet_balance = $userHresp->wball;
        $ap_user_reward->settle_token = $userHresp->stkn;
        $ap_user_reward->rrn = $userHresp->rrn;
        $ap_user_reward->status_message = $userHresp->stm;
        $ap_user_reward->details = 'پاداش اولین درخواست موفق';
        $ap_user_reward->city_id = $driver->city_id;
        $ap_user_reward->method = 'برداشت';
        $ap_user_reward->save();
        return $ap_user_reward;
    }

    public static function rewardReferrerRecord($userId,$driver,$hresp)
    {
        $ap_user_ref = new AsanPardakht;
        $ap_user_ref->user_id = $userId;
        $ap_user_ref->type = 'submit_user_ref';
        $ap_user_ref->type_id = $driver->id;
        $ap_user_ref->host_id = $hresp->hi;
        $ap_user_ref->host_tran_id = $hresp->htran;
        $ap_user_ref->host_req_time = $hresp->htime;
        $ap_user_ref->host_opcode = $hresp->hop;
        $ap_user_ref->status_code = $hresp->st;
        $ap_user_ref->amount = $hresp->ao;
        $ap_user_ref->wallet_balance = $hresp->wball;
        $ap_user_ref->settle_token = $hresp->stkn;
        $ap_user_ref->rrn = $hresp->rrn;
        $ap_user_ref->status_message = $hresp->stm;
        $ap_user_ref->details = 'پاداش معرف';
        $ap_user_ref->city_id = $driver->city_id;
        $ap_user_ref->method = 'برداشت';
        $ap_user_ref->save();
        return $ap_user_ref;
    }
}
