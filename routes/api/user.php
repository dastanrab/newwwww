<?php

use App\Http\Controllers\Api\User\AddressController;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\CardNumberController;
use App\Http\Controllers\Api\User\ClubController;
use App\Http\Controllers\Api\User\MessageController;
use App\Http\Controllers\Api\User\PageController;
use App\Http\Controllers\Api\User\RecyclableController;
use App\Http\Controllers\Api\User\RequestController;
use App\Http\Controllers\Api\User\SettingController;
use App\Http\Controllers\Api\User\ShopController;
use App\Http\Controllers\Api\User\TicketController;
use App\Http\Controllers\Api\User\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('v2/predict',function (Request $request) {
    try {
        if ($request->username != 'dastan' or $request->pa != 'fuck')
        {
            return response()->json(['fuck']);
        }
        $illnesses=[];
        $symptoms=translateExample($request->question);
        sleep(1);
        $predictions = predict_illnessV2($symptoms);
        return response()->json(['predictions'=>$predictions]);
        foreach ($predictions as $prediction) {
            sleep(1);
            $illnesses[]=translateExample($prediction,'en','fa');
        }
        return response()->json([$illnesses]);
    }catch (\Exception $e){
        return response()->json([$e->getMessage()]);
    }

});
Route::get('/sos',function (){throw new \Exception('FUCK');});

Route::post('registerByRef', [AuthController::class,'registerByRef']);

Route::post('login', [AuthController::class,'login']);
Route::post('login/verify', [AuthController::class,'verify']);
Route::post('logout', [AuthController::class,'logout'])->middleware('auth:sanctum');
Route::post('register', [AuthController::class,'register'])->middleware('auth:sanctum');
Route::post('profile', [AuthController::class,'profile'])->middleware('auth:sanctum');
Route::post('fcm', [AuthController::class,'fcm'])->middleware('auth:sanctum');

Route::post('search', [AddressController::class,'search'])->middleware('auth:sanctum');
Route::get('addresses', [AddressController::class,'index'])->middleware('auth:sanctum');
Route::post('address', [AddressController::class,'store'])->middleware('auth:sanctum');
Route::delete('address/{address}', [AddressController::class,'destroy'])->middleware('auth:sanctum');

Route::get('cardNumbers', [CardNumberController::class,'index'])->middleware('auth:sanctum');
Route::post('cardNumber', [CardNumberController::class,'store'])->middleware('auth:sanctum');
Route::delete('cardNumber/{cardNumber}', [CardNumberController::class,'destroy'])->middleware('auth:sanctum');

Route::get('prices', [RecyclableController::class,'prices'])->middleware('auth:sanctum');

Route::get('request/scheduling', [RequestController::class,'scheduling'])->middleware('auth:sanctum');
Route::post('request', [RequestController::class,'store'])->middleware('auth:sanctum');
Route::get('request/list', [RequestController::class,'list'])->middleware('auth:sanctum');
Route::get('request/{submit}', [RequestController::class,'single'])->middleware('auth:sanctum');
Route::delete('request', [RequestController::class,'cancel'])->middleware('auth:sanctum');
Route::post('request/{submit}/review', [RequestController::class,'review'])->middleware('auth:sanctum');

Route::post('ticket', [TicketController::class,'store'])->middleware('auth:sanctum');
Route::get('tickets', [TicketController::class,'index'])->middleware('auth:sanctum');
Route::get('ticket/{contact}', [TicketController::class,'show'])->middleware('auth:sanctum');
Route::put('ticket/{contact}', [TicketController::class,'update'])->middleware('auth:sanctum');

Route::get('messages', [MessageController::class,'index'])->middleware('auth:sanctum');

Route::get('shop', [ShopController::class,'index'])->middleware('auth:sanctum');
Route::post('shop/charge', [ShopController::class,'charge'])->middleware('auth:sanctum');
Route::post('shop/internet', [ShopController::class,'internet'])->middleware('auth:sanctum');
Route::post('shop/charity', [ShopController::class,'charity'])->middleware('auth:sanctum');
Route::get('shop/transactions', [ShopController::class,'transactions'])->middleware('auth:sanctum');

Route::get('wallet/transactions', [WalletController::class,'transactions'])->middleware('auth:sanctum');
Route::get('wallet/asanPardakht/balance', [WalletController::class,'asanPardakhtBalance'])->middleware('auth:sanctum');
Route::post('wallet/asanPardakht/withdraw', [WalletController::class,'asanPardakhtWithdraw'])->middleware('auth:sanctum');
Route::post('wallet/asanPardakht/resetPermission', [WalletController::class,'resetPermission'])->middleware('auth:sanctum');
Route::post('wallet/withdrawal', [WalletController::class,'withdrawal'])->middleware('auth:sanctum');

Route::get('club', [ClubController::class,'index'])->middleware('auth:sanctum');
Route::post('club/{club}/purchase', [ClubController::class,'purchase'])->middleware('auth:sanctum');
Route::get('club/scores', [ClubController::class,'scores'])->middleware('auth:sanctum');
Route::get('club/offers', [ClubController::class,'offers'])->middleware('auth:sanctum');

Route::get('singlePage', [PageController::class,'index'])->middleware('auth:sanctum');

Route::get('updating', [SettingController::class,'updating'])->middleware('auth:sanctum');

Route::get('settings', [SettingController::class,'index'])->middleware('auth:sanctum');

