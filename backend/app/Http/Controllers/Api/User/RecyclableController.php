<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\DriverWallet;
use App\Models\Recyclable;

class RecyclableController extends Controller
{

    /**
     * لیست قیمت ضایعات
     *
     * لیست کامل انواع ضایعات قابل بازیافت به‌همراه:
     * - توضیحات
     * - تصویر
     * - بیشترین قیمت
     * - قیمت واحد (۱ کیلو)
     * - جدول قیمت بر اساس وزن
     *
     * قیمت‌ها بر اساس نوع کاربر (حقیقی / حقوقی) محاسبه می‌شود.
     *
     * @group Recyclables
     * @authenticated
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "",
     *   "data": {
     *     "list": [
     *       {
     *         "id": 1,
     *         "title": "کاغذ و کارتن",
     *         "description": "مثال‌های قابل بازیافت...",
     *         "bgImage": "https://aniroob.com/wp-content/uploads/2024/09/recyclables-1.png",
     *         "image": "https://example.com/assets/img/icons/recyclables/1.png",
     *         "maxAmount": 3850,
     *         "unitAmount": 2450,
     *         "rateList": {
     *           "1": 2450,
     *           "2": 2450,
     *           "3": 2450,
     *           "4": 3150,
     *           "5": 3150,
     *           "10": 3500,
     *           "15": 3850,
     *           "30": 3850
     *         }
     *       }
     *     ]
     *   }
     * }
     */

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
