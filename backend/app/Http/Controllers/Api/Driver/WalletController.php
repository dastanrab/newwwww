<?php

namespace App\Http\Controllers\Api\Driver;

use App\Events\ActivityEvent;
use App\Http\Controllers\Controller;
use App\Models\AsanPardakht;
use App\Models\WalletDetails;
use App\Models\Cashout;
use App\Models\City;
use App\Models\Iban;
use App\Models\Inax;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function transactions()
    {
        $user = auth()->user();
        $paginate = 10;
        $data = ['list' => [], 'limit' => $paginate];
        $transactions = WalletDetails::where('user_id', $user->id)->latest()->paginate($paginate);
        foreach ($transactions as $transaction){
            $data['list'][] = [
                'id'       => $transaction->id,
                'type'     => $transaction->method == 'برداشت' ? 'decrease' : 'increase',
                'refCode' => $transaction->id,
                "date" => [
                    "day" => verta()->instance($transaction->created_at)->format('Y/m/d'),
                    "time" => verta()->instance($transaction->created_at)->format('H:i'),
                ],
                "amount" => $transaction->amount/10,
                "details" => $transaction->details,
            ];
        }
        return sendJson('success','',$data);

    }

    public function asanPardakhtBalance()
    {
        $user = auth()->user();
        $platform = request()->header('app-platform');
        $callback = "https://la.bazistco.com/asanpardakht/callback?platform=$platform";
        $ap = AsanPardakht::check($user->mobile,$callback);
        $data = [
            'hasAccess' => $ap['status'] === 1,
            'balance'   => $ap['status'] === 1 ? floor($ap['data']) : 0,
            'url'       => $ap['status'] === 2 ? $ap['data'] : '',
        ];
        return sendJson('success','',$data);
    }

    public function asanPardakhtWithdraw(Request $request)
    {
        $user = auth()->user();
        $minAmount = minWithdrawAapToman();
        $validator = Validator::make($request->all(),
            [
                'amount' => 'required|numeric|min:'.$minAmount,
            ],
            [
                'amount' => 'حداقل مبلغ برداشتی '.tomanFormat($minAmount).' می باشد',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }

        $wallet = Wallet::where('user_id', $user->id)->first();
        $amountToman = $request->amount;
        $amountRial = $request->amount*10;
        if ($amountToman > $wallet->wallet) {
            return sendJson('error','موجودی کافی نمی باشد');

        }


        try {
            $data = DB::transaction(function () use($user,$wallet,$amountRial,$amountToman){
                $city_id = $user->city->id;
                $withdraw = AsanPardakht::increase($user->mobile, intval($amountRial));
                //todo add transaction create
                $withdrawHresp = json_decode($withdraw['hresp']);
                if($withdrawHresp->st == 0){
                    $withdrawVerify = AsanPardakht::verify($withdrawHresp->htran, $withdrawHresp->htime, $withdrawHresp->ao, $withdrawHresp->stkn);
                    $withdrawVerifyHresp = json_decode($withdrawVerify['hresp']);
                    if($withdrawVerifyHresp->st == 0){
                        $wallet->wallet -= $amountToman;
                        $save = $wallet->save();
                        if($save) {
                            $withdrawRecord = AsanPardakht::withdrawRecord($user->id, $withdrawHresp, $city_id);
                            WalletDetails::create($city_id, $user->id, $wallet->id, 'cashout_to_aap', $withdrawRecord->id, $amountRial, $wallet->wallet * 10, 'برداشت', 'انتقال به کیف پول آپ');
                        }
                        else{
                            event(new ActivityEvent('انتقال به کیف پول انجام شد ولی از کیف پول کسر نشد', 'AsanPardakhtNotDecreaseBazistWallet', false));
                        }
                    }
                    else{
                        return sendJson('error', 'انتقال به کیف پول آپ با اشکال روبرو شد لطفا دوباره امتحان کنید');
                    }

                }
                else{
                    return sendJson('error', 'انتقال به کیف پول آپ با اشکال روبرو شد لطفا دوباره امتحان کنید');
                }
                $data = [
                    'aapBalance' => AsanPardakht::balance($user->mobile),
                    'bazistBalance' => $user->wallet->wallet,
                ];
                return $data;
            });
            return sendJson('success','اعتبار به کیف پول آپ انتقال یافت',$data);
        }
        catch (Exception $exception){
            return sendJson('error','خطایی پیش آمد لطفا با پشتیبانی تماس بگیرید');
        }
    }

    public function withdrawal(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
                'amount' => 'required|numeric|min:10000',
                'cardId' => 'required|exists:ibans,id,user_id,'.$user->id,
            ],
            [
                'amount' => 'حداقل مبلغ برداشتی '.tomanFormat(10000).' می باشد',
                'cardId' => 'شماره کارت به درستی انتخاب نشده است'
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        $taxToman = 700;
        $taxRial = $taxToman*10;
        $iban = Iban::find($request->cardId);
        $iban->iban = str_replace('IR','',$iban->iban);
        $amountToman = $request->amount;
        $wallet = Wallet::where('user_id', $user->id)->first();
        if($amountToman > $wallet->wallet){
            return sendJson('error','موجودی کافی نمی باشد');
        }

        try {
            $data = DB::transaction(function () use($user,$iban,$wallet,$amountToman,$taxToman,$taxRial){
                $city_id = $user->city->id;
//                if ($amountToman + $taxToman >= $wallet->wallet) { // اگر کل مبلغ برداشت بشه کارمزد ازش کم میشه
//                    $real_amount = $amountToman;
//                    $amountToman = $amountToman ;
////                    $walletBalance = $amountToman*10;
//                    $walletBalance = ($wallet->wallet-$amountToman)*10;
////                    $walletBalanceTax = 0;
//                    $walletBalanceTax = ($wallet->wallet-$real_amount-$taxToman)*10;
//                    $wallet->wallet -= $real_amount;
//                    if ($walletBalance<0)
//                    {
//                        $walletBalance=0;
//                    }
//                    if ($walletBalanceTax<0)
//                    {
//                        $walletBalanceTax=0;
//                    }
//                } else { // کارمزد از مبلغی که می خواد برداشت کنه کم میشه
//                    $wallet->wallet -= $amountToman + $taxToman;
//                    $walletBalance = ($wallet->wallet+$taxToman)*10;
//                    $walletBalanceTax = $wallet->wallet*10;
//                }
                $wallet->wallet -= $amountToman ;
                $walletBalance = ($wallet->wallet)*10;
                //todo add transaction create
                $wallet->save();
                $cashout = new Cashout;
                $cashout->user_id = $user->id;
                $cashout->name = $iban->name;
                $cashout->amount = $amountToman;
                $cashout->shaba_number = $iban->iban;
                $cashout->card_number = $iban->card;
                $cashout->operator_id = null;
                $cashout->status = 'waiting';
                $cashout->save();
                WalletDetails::create($city_id, $user->id, $wallet->id, 'cashout', $cashout->id, $amountToman * 10, $walletBalance, 'برداشت', 'واریز به حساب بانکی');
//                BazistWallet::create($city_id, $user->id, $wallet->id, 'cashout', $cashout->id, $taxRial, $walletBalanceTax, 'برداشت', 'کارمزد واریز به حساب بانکی');
                $data = [
                    'balance' => floor($wallet->wallet)
                ];
                return $data;
            });

            return sendJson('success','درخواست واریز به حساب شما با موفقیت ثبت شد',$data);
        }
        catch (Exception $exception) {
            return sendJson('error', 'درخواست واریز با اشکال روبرو شد لطفا دوباره امتحان کنید');
        }
    }

    public function resetPermission()
    {
        $user = auth()->user();
        $ap = AsanPardakht::resetPermission($user->mobile);
        $hresp = json_decode($ap['hresp']);
        if($hresp->st == 0){
            return sendJson('success','دسترسی آنیروب از کیف پول شما لغو شد');
        }
        return sendJson('error', 'خطایی پیش آمد لطفا دوباره امتحان کنید');
    }
}
