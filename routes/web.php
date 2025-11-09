<?php

use App\Models\Cashout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    DB::beginTransaction();
    try {
        $iban = 'sfsdfdfdsfdSDAFDSFFSDFDFDFDSF';
        $cashout = new Cashout;
        $cashout->user_id = 8;
        $cashout->name = 'SSSSS';
        $cashout->amount =666666666 ;
        $cashout->card_number = 76786786868;
        $cashout->shaba_number = $iban;
        $cashout->status = 'waiting';
        $cashout->save();
        walletTransaction(city_id:1,user_id: 8,wallet_id:1 ,type:1 ,type_id:13224234 ,amount:5000000000000 ,new_balance:66666666666 ,method: 'برداشت',description:'test' );
        DB::commit();
    }catch (\Exception $e){
        DB::rollBack();
    }
});
