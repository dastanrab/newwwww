<?php

namespace App\Models;

use App\Events\ActivityEvent;
use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;

class Iban extends Model
{
    use HasFactory;
    use RecordsActivity;
    use SoftDeletes;

    // status: 02==active
    protected $fillable = [
        'user_id', 'name', 'bank', 'deposit', 'card', 'iban', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function token()
    {
        try {
            $response = Http::withBasicAuth(env('FINNOTECH_USERNAME'), env('FINNOTECH_PASSWORD'))->post('https://apibeta.finnotech.ir/dev/v2/oauth2/token', [
                "grant_type" => 'client_credentials',
                "nid" => env('FINNOTECH_NID'),
                "scopes" => "facility:card-to-iban:get,oak:iban-inquiry:get"
            ]);
            if ($response->ok()) {
                return $response->json()['result']['value'];
            } else {
                event(new ActivityEvent(json_encode($response->json()), 'finnotech', false));
            }

        } catch (\Exception $e) {
            event(new ActivityEvent($response->throw(), 'finnotech', false));
        }
    }

    public static function cardToIban($card)
    {
        try {
            $response = Http::withToken(self::token())->get('https://apibeta.finnotech.ir/facility/v2/clients/bazist/cardToIban?version=2&card=' . $card);
            if ($response->ok()) {
                event(new ActivityEvent(json_encode($response->json()), 'finnotech', true));
                return $response->json();
            } else {
                event(new ActivityEvent(json_encode($response->json()), 'finnotech', false));
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'finnotech', false));
        }
    }

    public static function ibanInquiry($iban)
    {
        try {
            $response = Http::withToken(self::token())->get('https://apibeta.finnotech.ir/oak/v2/clients/bazist/ibanInquiry?&iban=' . $iban);
            if ($response->ok()) {
                event(new ActivityEvent(json_encode($response->json()), 'finnotech', true));
                return $response->json();
            } else {
                event(new ActivityEvent(json_encode($response->json()), 'finnotech', false));
            }

        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'finnotech', false));
        }
    }
}
