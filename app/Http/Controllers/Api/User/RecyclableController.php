<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\DriverWallet;
use App\Models\Recyclable;

class RecyclableController extends Controller
{
    public function prices()
    {
        $user = auth()->user();
        $recyclables = Recyclable::with(['percentages' => function($query) use($user) {
            $query->where('is_legal', $user->legal)->orderBy('price', 'desc');
        }])->get();
        foreach ($recyclables as $recyclable) {
            $rateList = [];
            foreach ($recyclable->percentages as $percentage){
                $rateList[(string)$percentage->weight] = $percentage->price;
            }
            ksort($rateList);
            $maxPrice = $recyclable->percentages[0]->price;
            $data['list'][] = [
                'id' => $recyclable->id,
                'title' => $recyclable->title,
                'description' => $recyclable->description,
                'bgImage' => "https://bazistco.com/wp-content/uploads/2024/09/recyclables-{$recyclable->id}.png",
                'image' => asset("assets/img/icons/recyclables/{$recyclable->id}.png"),
                'maxAmount' => $maxPrice,
                'unitAmount' => $recyclable->percentages->where('weight', 1)->first()->price,
                'rateList' => $rateList
            ];
        }

        return sendJson('success','',$data);
    }
}
