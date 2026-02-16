<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\BazistWallet;
use App\Models\Car;
use App\Models\Cashout;
use App\Models\City;
use App\Models\Driver;
use App\Models\Iban;
use App\Models\Referrer;
use App\Models\Submit;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class WalletIndexUserList extends Component
{
    #[Url]
    public $search;
    public $userId;
    public $ibanId;
    public $amount;
    public $description;
    public $type;
    public $submit_id;
    public $ref;
    public $fee = 700;
    public function render()
    {
        return view('livewire.dashboard.wallet.wallet-index-user-list');
    }

    #[Computed]
    public function users()
    {
        $query = User::with('roles');
        if(!empty($this->search) and auth()->user()->getRoles(0) != 'financial_manager'){
            $this->dispatch('filter-search',$this->search);
            if(is_numeric($this->search)){
                $query->where('mobile','LIKE',"%{$this->search}%");
            }
            else{
                $query->whereRaw("CONCAT(name, ' ', lastname) LIKE '%{$this->search}%'");
            }
        }
        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    #[Computed]
    public function walletSum()
    {
        $wallets_sum = Wallet::where('wallet', '!=', 0)->pluck('wallet')->sum();
        $cashout_sum = Cashout::whereIn('status', ['waiting', 'depositing'])->pluck('amount')->sum();
        return floor($wallets_sum+$cashout_sum);
    }

    public function selectUser($user_id)
    {
        $this->dispatch('get-user-id',$user_id);
    }

    public function setUserId($user_id)
    {
        $this->userId = $user_id;
    }

    public function withdraw()
    {
        if ($this->type == 'card')
        {
           $rule= 'required|numeric|min:'.$this->fee+1;
        }elseif($this->type == 'miss_ref'){
            $rule='nullable';
        } else{
            $rule='required|numeric|min:1';
        }
        $this->validate([
            'type'        => 'required|in:card,withdraw,deposit,miss_ref',
            'description' => in_array($this->type,['withdraw', 'deposit']) ? 'required|min:4' : 'nullable',
            'amount'      => $rule,
            'ibanId'      => $this->type == 'card' ? 'required|integer|exists:ibans,id' : 'nullable',
        ],
        [
            'type'        => 'نوع درخواست را به درستی وارد نمایید',
            'description' => 'توضیحات را کامل وارد نمایید',
            'amount.min'  => "مبلغ برداشت باید بیشتر از $this->fee تومان باشد",
            'amount'      => 'مبلغ را وارد نمایید',
            'ibanId'      => 'کارت انتخاب شده معتبر نیست',
        ]);
        $user_id = $this->userId;
        $user = User::find($user_id);

        if(!$this->userId){
            return sendToast(0,'مشکلی پیش آمد لطفا یکبار رفرش نمایید.');
        }

        $city_id = null;
        if($user->city){
            $city_id = $user->city->id;
        }
        else{
            return sendToast(0,'مشکلی پیش آمده است لطفا به برنامه نویس اطلاع دهید (۱۰۰۱)');
        }
        if($this->type == 'card') {
            try {
                $data = $this;
                $amount_req = DB::transaction(function () use($data,$user_id,$city_id){
                    $operator_id = auth()->id();
                    $amount_req = $data->amount;
                    $iban = Iban::find($data->ibanId);
                    $iban->iban = str_replace('IR', '', $iban->iban);
                    $oldWallet = Wallet::where('user_id', $user_id)->first();
                    $wallet = Wallet::where('user_id', $user_id)->first();
                    if ($iban && $iban->user_id != $user_id) {
                        sendToast(0, 'خطا در دستکاری اطلاعات');
                        return;
                    } elseif ($amount_req > $wallet->wallet) {
                        sendToast(0, 'مبلغ برداشت بیشتر از کیف پول کاربر است');
                        return;
                    }
//                    if ($amount_req + $data->fee >= $wallet->wallet) {
                        $wallet->wallet -= $amount_req;
//                        $amount_req = $amount_req - $data->fee;
//                    } else {
//                        $wallet->wallet -= $amount_req + $data->fee;
//                    }
                    $wallet->save();
                    $cashout = new Cashout;
                    $cashout->user_id = $user_id;
                    $cashout->name = $iban->name;
                    $cashout->amount = $amount_req;
                    $cashout->shaba_number = $iban->iban;
                    $cashout->card_number = $iban->card;
                    $cashout->operator_id = $operator_id;
                    $cashout->status = 'waiting';
                    $cashout->save();
                    $walletBalance = ($oldWallet->wallet - $amount_req) * 10;
//                    $walletBalanceFee = $walletBalance - 7000;
                    BazistWallet::create(
                        $city_id,
                        $user_id,
                        $wallet->id,
                        'cashout_admin',
                        $cashout->id,
                        $amount_req * 10,
                        $walletBalance,
                        'برداشت',
                        'واریز به حساب بانکی'
                    );
//                    BazistWallet::create(
//                        $city_id,
//                        $user_id,
//                        $wallet->id,
//                        'cashout_admin',
//                        $cashout->id,
//                        7000,
//                        $walletBalanceFee,
//                        'برداشت',
//                        'کارمزد واریز به حساب بانکی'
//                    );
                    /*$cashout->user()->update([
                        'cardholder' => $iban->name,
                        'card_number' => $iban->card,
                        'shaba_number' => $iban->iban
                    ]);*/
                    return $amount_req;
                });

                sendToast(1, 'مبلغ ' . number_format($amount_req) . ' تومان جهت واریز به حساب بانکی از کیف پول کاربر کسر شد');
            }
            catch (Exception $e){
                sendToast(0, $e->getMessage());
            }
        }
        elseif($this->type == 'withdraw'){
            $this->authorize('wallet_all_withdraw_bazist_wallet',Wallet::class);
            $wallet = Wallet::where('user_id', $user_id)->first();
            try {
                DB::transaction(function () use($wallet,$user_id,$city_id){
                    $wallet->wallet -= $this->amount;
                    $save = $wallet->save();
                    BazistWallet::create(
                        $city_id,
                        $user_id,
                        $wallet->id,
                        'withdraw_bazist_wallet',
                        $wallet->id,
                        $this->amount * 10,
                        $wallet->wallet*10,
                        'برداشت',
                        $this->description
                    );
                });
                sendToast(1, 'مبلغ ' . number_format($this->amount) . ' تومان از کیف پول کاربر کسر شد');
            }
            catch (Exception $e){
                sendToast(0, $e->getMessage());
            }
        }
        elseif($this->type == 'deposit'){
            $this->authorize('wallet_all_deposit_bazist_wallet',Wallet::class);
            try {
                $data = $this;
                DB::transaction(function () use($user_id,$data){
                    $user = User::find($user_id);
                    $wallet = Wallet::where('user_id', $user->id)->first();
                    $wallet->wallet = $wallet->wallet + $data->amount;
                    $wallet->save();
                    BazistWallet::create($user->city->id, $user->id, $wallet->id, 'deposit', $user->id, $data->amount * 10, $wallet->wallet * 10, 'واریز', $data->description);
                });
                sendToast(1, "مبلغ {$this->amount} تومان کیف پول آنیروب کاربر واریز شد.");
            }
            catch (Exception $e){
                sendToast(0, $e->getMessage());
            }
        }
        elseif($this->type == 'miss_ref'){
            if (!isset($this->submit_id) or !isset($this->ref))
            {
                return sendToast(0, 'شناسه درخواست و شناسه معرف الزامی است');
            }
            $this->authorize('wallet_all_deposit_bazist_wallet',Wallet::class);
            $submit=Submit::query()->where('id',$this->submit_id)->first();
            if (!$submit){
              return  sendToast(0, 'شناسه درخواست نامعتبر است');
            }
            $userRef = User::find($this->ref - User::refCode());
            if(!$userRef){
                return sendToast(0,'کد معرف اشتباه است');
            }
            $driver_exist=Driver::query()->where('user_id',$userRef->id)->where('submit_id',$submit->id)->exists();
            if(!$driver_exist){
                return  sendToast(0, 'درخواست متعلق به این کد معرف نیست');
            }
            $u=User::query()->find($submit->user->id);
            $has_ref=Referrer::where('referrer_id', $u->id)->first();
            if(isset($u->refrral_code) or $has_ref)
            {
                return  sendToast(0, 'برای این کاربر معرف ثبت شده است');
            }
            try {
                $userRef->referrers()->create(['user_id' => $userRef->id, 'referrer_id' => $u->id]);
                $u->referral_code = $this->ref;
                $u->save();
                DB::transaction(function () use ($submit){
                    $referrer = Referrer::where('referrer_id', $submit->user->id)->first();
                    if ($referrer) {
                        if ($referrer->rewarded_at == null) {
                            $submits = Submit::where('user_id', $submit->user->id)->where('status', 3)->with('drivers.receives')->get();
                            if ($submits->count() > 0) {
                                $user_ref = User::find($referrer->user_id);
                                $sum_weight = $submits[0]->drivers[0]->receives->pluck('weight')->sum();
                                if ($sum_weight >= 10) {
                                    $walletRef = Wallet::where('user_id', $user_ref->id)->first();
                                    $walletRef->wallet = $walletRef->wallet + referrerRewardToman();
                                    $save = $walletRef->save();
                                    if($save) {
                                        BazistWallet::create($submit->city_id, $user_ref->id, $walletRef->id, 'submit_user_ref', $submit->driver->id, referrerRewardRial(), $walletRef->wallet * 10, 'واریز', 'پاداش معرف');
                                    }
                                }
                                if ($sum_weight >= 50) {
                                    $car = Car::where('user_id', $user_ref->id)->where('is_active', 1)->first();
                                    if ($car) {
                                        $walletRef = Wallet::where('user_id', $user_ref->id)->first();
                                        $walletRef->wallet = $walletRef->wallet + referrerRewardAbove50KiloToman();
                                        $save = $walletRef->save();
                                        if($save) {
                                            BazistWallet::create($submit->city_id, $user_ref->id, $walletRef->id, 'submit_user_ref', $submit->driver->id, referrerRewardAbove50KiloRial(), $walletRef->wallet * 10, 'واریز', 'پاداش معرف');
                                        }
                                    }
                                }
                            }
                        }
                    }
                });
               return sendToast(1, "پاداش معرف به کیف پول واریز شد");

            }catch (Exception $exception)
            {
                sendToast(0, $exception->getMessage());
            }

        }
    }
}
