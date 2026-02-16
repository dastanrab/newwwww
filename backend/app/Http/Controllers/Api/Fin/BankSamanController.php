<?php

namespace App\Http\Controllers\Api\Fin;

use App\Http\Controllers\Controller;
use App\Models\BankSaman;
use App\Models\Cashout;
use Illuminate\Http\Request;

class BankSamanController extends Controller
{
    public function result()
    {
        return false;
        $cashouts = Cashout::where('bank', 'SB24')->where('status', 'depositing')->whereNotIn('id',[84815,84765,84671,84638,84611,84541,84227,83637,83116,78304,76478,74187,54443, 58344, 61902, 65605,65619,71327,71819,72211,72513,73088, 75035])->offset(0)->take(40)->get();
        foreach ($cashouts as $cashout) {
            $result = BankSaman::result($cashout->bank_id);
            if(auth()->id() == developerId()){
//                dump($result);
            }
            if ($result) {
                if(auth()->id() == developerId()){
                    dump('here ',$cashout->bank_id);
                }
                if (in_array($result['result']['order']['status'], ['EVENT_DONE'])) {
                    if(auth()->id() == developerId()){
//                        dump('order is done');
                    }
                    if (in_array($result['result']['transactions'][0]['status'], ['TRANSACTION_TRANSFERRED', 'TRANSACTION_REVERSED', 'TRANSACTION_SETTLED'])) {
                        if(auth()->id() == developerId()){
//                            dump('transactions in done status',$result['result']['transactions'][0]);
                        }
                        $referenceNumber = $result['result']['transactions'][0];
                        if (array_key_exists('referenceNumber', $referenceNumber)) {
                            if(auth()->id() == developerId()){
                                dump('referenceNumber',$referenceNumber);
                            }
                            $cashout->trace_code = $referenceNumber['referenceNumber'];
                            $cashout->status = 'deposited';
                            $cashout->save();
                            //todo add transaction
                        }
                    }
                }
            }
        }

    }
}
