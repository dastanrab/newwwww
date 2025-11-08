<?php

namespace App\Models;

use App\Events\ActivityEvent;
use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Isun extends Model
{
    use HasFactory;
    use RecordsActivity;

    public static function balance()
    {
        try {
            $response = Http::withBasicAuth(env('ISUN_USERNAME'), env('ISUN_PASSWORD'))
                ->withOptions([
                    'verify' => false
                ])
                ->get('https://10.8.235.35:8443/rms/v1/topup/credit-balance');

            return $response->json()['resource']['balance'] / 10;

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'ISUN', false));
        }
    }

    public static function sharjMobile($mobile, $operator, $amount, $topupType='NORMAL')
    {
        try {
            $response = Http::withBasicAuth(env('ISUN_USERNAME'), env('ISUN_PASSWORD'))
                ->withOptions([
                    'verify' => false
                ])
                ->post('https://10.8.235.35:8443/rms/v1/topup', [
                    "subscriberNo" =>  $mobile,
                    "operator" => $operator,
                    "amount" => $amount * 10,
                    "traceCode" => mt_rand(100000, 999999),
                    "dateTime" => date('Y-m-d\TH:i:sO'),
                    "topupType" => $topupType,
                    "terminalType" => "INTERNET",
                ]);

            if ($response->ok()) {
                return $response->json();
            } else {
                return false;
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'ISUN', false));
        }
    }

    public static function sharjInternet($mobile, $operator, $amount, $packageCode)
    {
        try {
            $response = Http::withBasicAuth(env('ISUN_USERNAME'), env('ISUN_PASSWORD'))
                ->withOptions([
                    'verify' => false
                ])
                ->post('https://10.8.235.35:8443/rms/v1/topup', [
                    "subscriberNo" =>  $mobile,
                    "operator" => $operator,
                    "amount" => $amount * 10,
                    "traceCode" => mt_rand(100000, 999999),
                    "dateTime" => date('Y-m-d\TH:i:sO'),
                    "topupType" => "INTERNET",
                    "packageCode" => $packageCode,
                    "terminalType" => "INTERNET"
                ]);

            if ($response->ok()) {
                return $response->json();
            } else {
                return false;
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'ISUN', false));
        }
    }

    public static function internetPackage($operator)
    {
        try {
            $response = Http::withBasicAuth(env('ISUN_USERNAME'), env('ISUN_PASSWORD'))
                ->withOptions([
                    'verify' => false
                ])
                ->get('https://10.8.235.35:8443/rms/v1/topup/special-package?operator=' . $operator);

            if ($response->ok()) {
                return $response->json()['resources'];
            } else {
                return false;
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'ISUN', false));
        }
    }
}
