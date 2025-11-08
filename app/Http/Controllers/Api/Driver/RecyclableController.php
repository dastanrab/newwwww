<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\DriverWallet;
use App\Models\Recyclable;
use Illuminate\Http\Request;

class RecyclableController extends Controller
{
    public function prices()
    {
        $user = auth()->user();
        $recyclables = Recyclable::with(['percentages'])->get();
        foreach ($recyclables as $recyclable) {
            $driverMaxPrice = null;
            if(DriverWallet::where('user_id', $user->id)->first()){
                $driverMaxPrice = $recyclable->max_price;
            }

            $data['list'][] = [
                'id' => $recyclable->id,
                'title' => $recyclable->title,
                'description' => $recyclable->description,
                'image' => asset("assets/img/icons/recyclables/{$recyclable->id}.png"),
                'amount' => [
                    'guild' => [
                        'max' => $recyclable->percentages->where('is_legal', 1)->sortByDesc('price')->first()->price,
                        'min' => $recyclable->percentages->where('is_legal', 1)->where('weight', 1)->first()->price,
                    ],
                    'citizen' => [
                        'max' => $recyclable->percentages->where('is_legal', 0)->sortByDesc('price')->first()->price,
                        'min' => $recyclable->percentages->where('is_legal', 0)->where('weight', 1)->first()->price,
                    ],
                    'driver' => $driverMaxPrice,
                ]
            ];
        }

        return sendJson('success','',$data);
    }
}
