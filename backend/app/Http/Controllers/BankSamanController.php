<?php

namespace App\Http\Controllers;

use App\Models\BankSaman;
use App\Models\Cashout;
use Illuminate\Http\Request;

class BankSamanController extends Controller
{
    public function result()
    {
        return false;
//        ->whereNotIn('id',[84815,84765,84671,84638,84611,84541,84227,83637,83116,78304,76478,74187,54443, 58344, 61902, 65605,65619,71327,71819,72211,72513,73088, 75035,84816
//        ,84839,84903,84916,84922,85007,85189,85405,85457,85468,85772,85774,85787,86011,86020,86040,86076,86088,
//        86143,86302,86399,86440,86480,86484,86491,86680,86766,86769,86876,86903,86910,86949,86969,87035
//        ,87074])
        $cashouts = Cashout::where('bank', 'SB24')->where('status', 'depositing')->where('id','>',88374)->offset(0)->take(50)->get();
        foreach ($cashouts as $cashout) {
            $result = BankSaman::result($cashout->bank_id);
            if(auth()->id() == developerId()){
                dump($cashout->id);
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
