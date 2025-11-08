<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Iban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CardNumberController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $ibans = $user->ibans()->get();
        $data = [];
        foreach ($ibans as $iban){
            $data[] = [
                'value' => $iban->id,
                'label' => $iban->card,
                'name' => $iban->name,
                'bank' => $iban->bank,
            ];
        }
        return sendJson('success', '', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'cardNumber'     => 'required|digits:16',
            ],
            [
                'cardNumber'    => 'شماره کارت را به درستی وارد نمایید',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        $user = auth()->user();
        $ibanExists = $user->ibans()->where('card',$request->cardNumber)->count();
        if ($ibanExists) {
            return sendJson('error','این شماره کارت قبلا ثبت شده');
        }
        $card_to_iban = Iban::cardToIban($request->cardNumber);
        //$card_to_iban = false;
        if (!$card_to_iban) {
            return sendJson('error', 'شماره کارت وارد شده نادرست است.');
        }
        $iban = $user->ibans()->create([
            'name'    => preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', explode(' / فعال', $card_to_iban['result']['depositOwners'])[0]),
            'bank'    => $card_to_iban['result']['bankName'],
            'deposit' => $card_to_iban['result']['deposit'],
            'card'    => $request->cardNumber,
            'iban'    => $card_to_iban['result']['IBAN'],
            'status'  => $card_to_iban['result']['depositStatus'],
        ]);
        if($iban){
            $data = [
                'value' => $iban->id,
                'label' => $iban->card,
                'name' => $iban->name,
                'bank' => $iban->bank,
            ];
            return sendJson('success','شماره کارت افزوده شد', $data);
        }
        return sendJson('error', 'ثبت کارت با خطا مواجه شد لطفا دوباره امتحان کنید');
    }

    public function destroy(Iban $cardNumber)
    {
      if (auth()->user()->id == $cardNumber->user_id)
      {
          Iban::query()->where('id',$cardNumber->id)->delete();
          return sendJson('success','شماره کارت حذف شد', []);
      }
        return sendJson('error', 'شماره مجاز به حذف این شماره کارت نیستید');

    }
}
