<?php

use App\Http\Controllers\Api\Driver\AuthController;
use App\Http\Controllers\Api\Driver\RecyclableController;
use App\Http\Controllers\Api\Driver\RequestController;
use App\Http\Controllers\Api\Driver\RollcallController;
use App\Http\Controllers\Api\Driver\SettingController;
use App\Http\Controllers\Api\Driver\UserController;
use App\Http\Controllers\Api\Driver\WalletController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class,'login']);
Route::post('login/verify', [AuthController::class,'verify']);
Route::post('fcm', [AuthController::class,'fcm'])->middleware('auth:sanctum,driver');

Route::post('rollCall', [RollcallController::class,'rollCall'])->middleware('auth:sanctum,driver');

Route::get('prices', [RecyclableController::class,'prices'])->middleware('auth:sanctum,driver');

Route::post('requests/map', [RequestController::class,'map'])->middleware('auth:sanctum,driver');
Route::get('requests/current', [RequestController::class,'currentList'])->middleware('auth:sanctum,driver');
Route::get('requests/history', [RequestController::class,'historyList'])->middleware('auth:sanctum,driver');
Route::post('request/{submit}/receive', [RequestController::class,'receive'])->middleware('auth:sanctum,driver');
Route::post('request/{submit}/message', [RequestController::class,'storeMessage'])->middleware('auth:sanctum,driver');
Route::post('request/{submit}/waste', [RequestController::class,'storeWaste'])->middleware('auth:sanctum,driver');
Route::delete('request/waste/{receive}', [RequestController::class,'destroyWaste'])->middleware('auth:sanctum,driver');
Route::post('request/{submit}/done', [RequestController::class,'done'])->middleware('auth:sanctum,driver');
Route::post('request/attendance', [RequestController::class,'attendance'])->middleware('auth:sanctum,driver');

Route::post('client/check', [UserController::class,'userCheck'])->middleware('auth:sanctum,driver');
Route::post('client/register', [UserController::class,'register'])->middleware('auth:sanctum,driver');
Route::post('client/address', [UserController::class,'storeAddress'])->middleware('auth:sanctum,driver');
Route::post('client/scheduling', [UserController::class,'scheduling'])->middleware('auth:sanctum,driver');
Route::post('client/request', [UserController::class,'storeRequest'])->middleware('auth:sanctum,driver');

Route::get('settings', [SettingController::class,'index'])->middleware('auth:sanctum,driver');
Route::get('wallet/transactions', [WalletController::class,'transactions'])->middleware('auth:sanctum,driver');
Route::get('wallet/asanPardakht/balance', [WalletController::class,'asanPardakhtBalance'])->middleware('auth:sanctum,driver');
Route::post('wallet/asanPardakht/withdraw', [WalletController::class,'asanPardakhtWithdraw'])->middleware('auth:sanctum,driver');
Route::post('wallet/asanPardakht/resetPermission', [WalletController::class,'resetPermission'])->middleware('auth:sanctum,driver');
Route::post('wallet/withdrawal', [WalletController::class,'withdrawal'])->middleware('auth:sanctum,driver');
Route::post('logout', [AuthController::class,'logout'])->middleware('auth:sanctum,driver');
