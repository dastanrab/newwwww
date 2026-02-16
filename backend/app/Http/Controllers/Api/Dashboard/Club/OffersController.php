<?php

namespace App\Http\Controllers\Api\Dashboard\Club;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubCategory;
use App\Models\Offer;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OffersController extends Controller
{
    use AuthorizesRequests;


    public function index()
    {
        $listType = request()->get('listType', 0); // دریافت از GET

        $offers = Offer::with(['user','club'])
            ->when($listType == 1, fn($q) => $q->where('used', 1))
            ->when($listType != 1, fn($q) => $q->where('used', 0))
            ->orderBy('id', 'DESC')
            ->paginate(20);
       return success_response($offers);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:2',
            'count' => 'required|integer|min:1',
            'club'  => 'required|integer|exists:clubs,id',
        ],[
            'title.required' => 'عنوان اجباری می باشد',
            'count.required' => 'تعداد اجباری می باشد',
            'title.min'      => 'حداقل کاراکتر عنوان :min می باشد',
        ]);

        $club = Club::findOrFail($request->club);

        try {
            for ($i = 0; $i < (int)$request->count; $i++) {

                // generate unique 6-digit code
                do {
                    $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                } while (Offer::where('code', $code)->exists());

                Offer::create([
                    'club_id' => $club->id,
                    'score'   => $club->score,
                    'title'   => $request->title,
                    'code'    => $code,
                    'used'    => 0
                ]);
            }

            return success_response($club, 'با موفقیت ایجاد شد', 201);

        } catch (\Exception $e) {
            return error_response(
                'ثبت با اشکال روبرو شد',
                500,
                $e->getMessage()
            );
        }
    }


    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        $offer->delete();

        return success_response([$offer], 'با موفقیت حذف شد', 201);

    }





}
