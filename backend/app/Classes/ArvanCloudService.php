<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class ArvanCloudService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.arvan.api_key');
        $this->baseUrl = 'https://api.arvancloud.ir';
    }

    public function getCredit()
    {
        $response = Http::withHeaders([
            'Authorization' => 'apikey 7292c8d2-de6f-576f-b30a-50c43ab27d7e',
        ])->get('https://napi.arvancloud.ir/accounts/v1/wallet');

        if ($response->successful()) {
            return $response->json(); // یا فقط مقدار اعتبار مانند ['credit' => 12500]
        }

        throw new \Exception('Failed to get credit from ArvanCloud');
    }

}
