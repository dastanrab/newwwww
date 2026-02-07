<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\AsanPardakht;
use App\Models\BazistWallet;
use App\Models\Cashout;
use App\Models\Driver;
use App\Models\Inax;
use App\Models\Submit;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class WalletIndexList extends Component
{
    use WithPagination;
    public $user_id;
    public $user;
    #[Url]
    public $filter;
    public $row;
    public $no_deposite_amounts = 0;
    public $aap_deposite_amounts = 0;
    public  $bazist_deposite_amounts = 0;
    public $user_waste_amounts;
    public $user_paadaash;
    public  $aap_user_paadaash;
    /**
     * @var int|mixed
     */

    public  $inax_bardasht;
    /**
     * @var int|mixed
     */
    public  $cashout_bardasht;
    public  $aap_bardasht;
    public  $admin_bardasht;
    private $charge_back_deposite;
    private  $return_inax_deposit;
    /**
     * @var int|mixed
     */
    private  $back_to_bazist_wallet;

    public function boot()
    {
        $wallet_id=Wallet::query()->where('user_id',$this->user_id)->first()->id;
        $this->inax_bardasht=BazistWallet::where('wallet_id',$wallet_id)->where('method','برداشت')->whereIn('type', ['sharj_mobile','sharj_internet'])->sum('amount')/10;
        $this->return_inax_deposit=BazistWallet::where('wallet_id',$wallet_id)->where('method','واریز')->whereIn('type', ['back_mobile','back_internet'])->sum('amount')/10;
//        $bardasht_types= DB::table('asan_pardakhts')
//            ->select('type')
//            ->where('method', 'برداشت')
//            ->groupBy('type')
//            ->get()->pluck('type')->toArray();
//        $amount=DB::table('asan_pardakhts')
//            ->select('amount')
//            ->where('method', 'برداشت')
//            ->where('user_id',$this->user_id)
//            ->whereIn('type',$bardasht_types)
//            ->sum('amount');
        $admin_bardasht=BazistWallet::where('wallet_id',$wallet_id)->where('method','برداشت')->where('type', 'withdraw_bazist_wallet')->sum('amount');
//        $this->charge_bardasht=BazistWallet::where('user_id',$this->user_id)->where('method','برداشت')->whereIn('type', ['sharj_mobile','sharj_internet'])->sum('amount');
        $wallet_to_aap=BazistWallet::where('wallet_id',$wallet_id)->where('method','برداشت')->where('type', 'cashout_to_aap')->sum('amount');
        $this->admin_bardasht=$admin_bardasht>0?$admin_bardasht/10:0;
        $this->aap_bardasht=$wallet_to_aap>0?$wallet_to_aap/10:0;
//        $this->cashout_bardasht=Cashout::query()->whereIn('status',['deposited','depositing','waiting'])->where('user_id',$this->user_id)->sum('amount');
        $cashout_bardasht=BazistWallet::query()->where('method','برداشت')->whereIn('type',['cashout_admin','cashout','submit','submit_phone','submit_phone_all'])->where('wallet_id',$wallet_id)->whereNot('details','کارمزد واریز به حساب بانکی')->sum('amount');
        $this->cashout_bardasht=$cashout_bardasht>0?$cashout_bardasht/10:0;
        $app_total=AsanPardakht::query()->where('user_id',$this->user_id)->whereNot('type','to_aap')->sum('amount');
        $waste_total=\App\Models\Submit::where('user_id',$this->user_id)->whereNot('cashout_type','aap')->where('status',3)->sum('total_amount');
        $total_paadaash=BazistWallet::where('wallet_id',$wallet_id)->where('method','واریز')->whereIn('type', ['first_submit_user','submit_user_ref'])->sum('amount');
        $total_aap_paadaash=AsanPardakht::query()->where('user_id',$this->user_id)->whereIn('type', ['first_submit_user','submit_user_ref','reward_15_50000','mothers_day_1400','fathers_day_1400','mabas_1400','submit_user_ref_rps'])->sum('amount');
        $this->user_waste_amounts=$waste_total;
        $this->user_paadaash=$total_paadaash > 0?$total_paadaash/10:0;
        $this->aap_user_paadaash=$total_aap_paadaash > 0?$total_aap_paadaash/10:0;
        $this->aap_deposite_amounts=$app_total>0?$app_total/10:0;
        $this->charge_back_deposite=BazistWallet::where('wallet_id',$wallet_id)->where('method','واریز')->whereIn('type', ['back_mobile','back_internet'])->sum('amount');
        $submit_ids=DB::table('submits')->select('id')->where('user_id', $this->user_id)
            ->where('status', 3)->whereNot('cashout_type','aap')->pluck('id')->toArray();
        $deposite_drivers = DB::table('wallet_details')
            ->whereIn('type_id', function ($query) use ($submit_ids) {
                $query->select('id')
                    ->from('drivers')
                    ->whereIn('submit_id',$submit_ids);
            })
            ->select(['type_id','amount'])
            ->whereNot('type','back_to_bazist_wallet')
            ->where('wallet_id',$wallet_id)
            ->where('method', 'واریز');
        $baz_total=$deposite_drivers;
        $back_to_bazist_wallet=DB::table('wallet_details')->where('type','back_to_bazist_wallet')->where('wallet_id',$wallet_id)->where('method', 'واریز')->sum('amount');
        $this->back_to_bazist_wallet=$back_to_bazist_wallet?$back_to_bazist_wallet/10:0;
        $deposite_drivers = $deposite_drivers->get()->pluck('type_id')->toArray();
        $deposite_submits = DB::table('drivers')->select('submit_id')->whereIn('id', $deposite_drivers)->pluck('submit_id')->toArray();
        $not_deposite_submits=array_diff($submit_ids, $deposite_submits);
        $baz_total=$baz_total->sum('amount');
        $this->bazist_deposite_amounts=$baz_total>0?$baz_total/10:0;
        $this->no_deposite_amounts=DB::table('submits')->select(['total_amount'])->whereIn('id',$not_deposite_submits)->sum('total_amount');
        $admin_deposite= DB::table('wallet_details')->where('type','deposit')->where('wallet_id',$wallet_id)->where('method', 'واریز')->sum('amount');
        $this->admin_deposite=$admin_deposite>0?$admin_deposite/10:0;
        $tax=DB::table('wallet_details')->where('details','کارمزد واریز به حساب بانکی')->where('wallet_id',$wallet_id)->where('method', 'برداشت')->sum('amount');
        $this->tax=$tax>0?$tax/10:0;
        $this->user = User::find($this->user_id);
    }

    public function render()
    {
        $this->authorize('wallet_all_single',Wallet::class);
        if($this->filter){
            $fn = $this->filter;
            $this->$fn();
        }
        return view('livewire.dashboard.wallet.wallet-index-list');
    }

    #[Computed]
    public function asanpardakht()
    {

        $query = AsanPardakht::with('user')->where('user_id',$this->user_id);
        return $query->latest()->paginate($this->row, pageName: 'ap-page');
    }

    #[Computed]
    public function ballance()
    {
        $wallet_ids = Wallet::query()->where('user_id',$this->user_id)->get()->pluck('id');
        return DB::table('wallet_details')
            ->selectRaw(" wallet_id ,
        SUM(IF(method = 'واریز', amount, 0)) AS vaariz_amount,
        SUM(IF(method = 'برداشت', -CAST(amount AS SIGNED), 0)) AS bardaasht_amount,
        SUM(IF(method = 'واریز', amount, -CAST(amount AS SIGNED))) AS balance_amount
    ")
            ->whereIn('wallet_id', $wallet_ids)
            ->groupBy('wallet_id')
            ->get();
    }
    #[Computed]
    public function bazist()
    {
        $wallet_id=Wallet::query()->where('user_id',$this->user_id)->first()->id;
        $query = BazistWallet::with('user')->where('wallet_id',$wallet_id);
        return $query->orderByDesc('id')->paginate($this->row, pageName: 'bazist-page');
    }
    #[Computed]
    public function not_deposites()
    {
        $wallet_id=Wallet::query()->where('user_id',$this->user_id)->first()->id;
        $user_id=$this->user_id;
        $submit_ids=DB::table('submits')->select('id')->where('user_id', $user_id)
            ->where('status', 3)->whereNot('cashout_type','aap')->pluck('id')->toArray();
        $deposite_drivers = DB::table('wallet_details')
            ->whereIn('type_id', function ($query) use ($submit_ids) {
                $query->select('id')
                    ->from('drivers')
                    ->whereIn('submit_id',$submit_ids);
            })
            ->select(['type_id','amount'])
            ->where('wallet_id',$wallet_id)
            ->where('method', 'واریز');
           $deposite_drivers = $deposite_drivers->get()->pluck('type_id')->toArray();
           $deposite_submits = DB::table('drivers')->select('submit_id')->whereIn('id', $deposite_drivers)->pluck('submit_id')->toArray();
           $not_deposite_submits=array_diff($submit_ids, $deposite_submits);
            return  DB::table('submits')->select(['id','total_amount','status','cashout_type','start_deadline'])->whereIn('id',$not_deposite_submits)->orderByDesc('id')->paginate($this->row??15, pageName: 'bazist-page');
    }

    #[Computed]
    public function cashouts()
    {
        $query = Cashout::with('user')->where('user_id',$this->user_id);
        return $query->latest()->paginate($this->row, pageName: 'cashout-page');
    }

    #[Computed]
    public function charge_internet()
    {
        $query = Inax::with('user')->where('user_id',$this->user_id);
        return $query->latest()->paginate($this->row, pageName: 'charge-internet-page');
    }
    #[On('pay')]
    public function pay(Submit $submit)
    {
        $driver=Driver::query()->where('submit_id',$submit->id)->where('status',3)->first();
        if ($submit->user_id != $this->user_id) {
            return sendToast(0, "شناسه به این کاربر تعلق ندارد");
        }
        if(!$driver){
            return sendToast(0, "شناسه معتبر نیست");
        }
        $check_exist=BazistWallet::query()->where('type_id',$driver->id)->where('user_id',$submit->user_id)->where('method','واریز')->first();
        if($check_exist){
            return sendToast(0, "شناسه قبلا ثبت شده");
        }
            try {
                DB::beginTransaction();
                $description='ثبت درخواست واریز نشده';
                $user=User::query()->where('id',$submit->user_id)->first();
                $wallet = Wallet::where('user_id', $submit->user_id)->first();
                $amount=$submit->total_amount;
                $balance= $wallet->wallet>0?$wallet->wallet:0;
                BazistWallet::create($user->city->id, $user->id, $wallet->id, 'add_miss_submit',  $driver->id, $amount * 10, $balance * 10, 'واریز', $description);
                DB::commit();
                sendToast(1, "ثبت شد");
            }
            catch (Exception $e){
                DB::rollBack();
                sendToast(0,'خطا در ثبت');
            }

    }

}
