<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use App\Events\ActivityEvent;

class BankSaman extends Model
{
    use HasFactory;
    use RecordsActivity;

    public static function token()
    {
        try {
            $response = Http::post('https://b2bapi.sb24.ir:8443/api/v1/auth/token', [
                "client_id" => env('SB24_CLIENT_ID'),
                "client_secret" => env('SB24_CLIENT_SECRET'),
                "scope" => ""
            ]);

            if ($response->ok()) {
                return $response->json()['result']['access_token'];
            } else {
                event(new ActivityEvent(json_encode($response->json()), 'SB24', false));
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'SB24', false));
        }
    }

    public static function init($token, $time, $amounts, $number)
    {
        try {
            $response = Http::withToken($token)->post('https://b2bapi.sb24.ir:8443/api/v1/b2bapi/payments', [
                "trackingId" => "",
                "name" => "submit" . "_" . $time,
                "description" => "خرید شهروندان" . " " . $time,
                "sourceIban" => env('SB24_IBAN'),
                "amount" => $amounts,
                "NumberOfTransactions" => $number,
            ]);
            if ($response->ok()) {
                event(new ActivityEvent(json_encode($response->json()), 'SB24', true));
                return $response->json();
            } else {
                event(new ActivityEvent(json_encode($response->json()), 'SB24', false));
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'SB24', false));
        }
    }

    public static function order($token, $time, $id, $amount, $iban, $name)
    {
        try {
            $response = Http::withToken($token)
                ->post('https://b2bapi.sb24.ir:8443/api/v1/b2bapi/payments/' . $id . '/transactions', [
                    "transactions" => [
                        [
                            "trackingId" => "",
                            "destinationIban" => $iban,
                            "destinationAccountOwner" => $name,
                            "description" => "خرید شهروندان" . " " . $time,
                            "amount" => $amount,
                            "paymentNumber" => "",
                            "reasonCode" => 'GPAC'
                        ]
                    ]
            ]);
            if ($response->ok()) {
                event(new ActivityEvent(json_encode($response->json()), 'SB24', true));
                return $response->json();
            } else {
                event(new ActivityEvent(json_encode($response->json()), 'SB24', false));
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'SB24', false));
        }
    }

    public static function confirm($amounts, $number, $amount, $iban, $name)
    {
        $time = time();
        $token = self::token();
        $init = self::init($token, $time, $amounts, $number);
        $id = $init['result']['id'];
        self::order($token, $time, $id, $amount, $iban, $name);

        try {
            $response = Http::withToken($token)->put('https://b2bapi.sb24.ir:8443/api/v1/b2bapi/payments/' . $id, [
                "status" => "confirmed"
            ]);
            if (auth()->id() == developerId())
            {
                dump($response->body());
            }
            if ($response->ok()) {
                event(new ActivityEvent(json_encode($response->json()), 'SB24', true));
                return $response->json();
            } else {
                event(new ActivityEvent(json_encode($response->json()), 'SB24', false));

            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'SB24', false));
        }
    }

    public static function result($id)
    {
        try {
            $response = Http::withToken(self::token())
                ->get('https://b2bapi.sb24.ir:8443/api/v1/b2bapi/payments/' . $id . '/transactions');
            if ($response->ok()) {
                event(new ActivityEvent(json_encode($response->json()), 'SB24', true));
                return $response->json();
            } else {
                event(new ActivityEvent(json_encode($response->json()), 'SB24', false));
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'SB24', false));
        }
    }

}
