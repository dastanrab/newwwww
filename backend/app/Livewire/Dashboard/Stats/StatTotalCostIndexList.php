<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\AsanPardakht;
use App\Models\BazistWallet;
use App\Models\Cashout;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatTotalCostIndexList extends Component
{
    #[Url]
    public $date;
    public function mount()
    {
        if(!$this->date){
            $this->date = verta()->format('Y/m/d');
            $this->dispatch('date',$this->date);
        }
    }

    public function render()
    {
        $date = verta()->parse($this->date)->toCarbon();
        $aap_total_deposit = AsanPardakht::whereDate('created_at', '<=', $date)->whereDate('created_at', '>=', '2023-10-09')->where('method', 'واریز')->sum('amount');
        $aap_total_withdraw = AsanPardakht::whereDate('created_at', '<=', $date)->whereDate('created_at', '>=', '2023-10-09')->where('method', 'برداشت')->sum('amount');
        $app_total = number_format(($aap_total_deposit - $aap_total_withdraw)/10);
        $aap_deposit = number_format(AsanPardakht::whereDate('created_at', $date)->where('method', 'واریز')->sum('amount')/10);
        $app_withdraw = number_format(AsanPardakht::whereDate('created_at', $date)->where('method', 'برداشت')->sum('amount')/10);
        $base_bazist_wallet = 2048991482;
        $bazist_deposit  = BazistWallet::whereDate('created_at', '<=', $date)->whereDate('created_at', '>', '2024-02-23')->where('method', 'واریز')->sum('amount');
        $bazist_withdraw = BazistWallet::whereDate('created_at', '<=', $date)->whereDate('created_at', '>', '2024-02-23')->where('method', 'برداشت')->sum('amount');
//        $bazist_wallet = number_format(($base_bazist_wallet + $bazist_deposit - $bazist_withdraw)/10);
        $cashout = number_format(Cashout::whereDate('created_at', $date)->where('trace_code', '!=', null)->sum('amount'));

        // 65332 - 09966121053
        // 64602 - 09966121054
        $office_deposit = BazistWallet::whereDate('created_at', $date)->whereIn('user_id',[65332,64602])->where('method','واریز')->sum('amount')/10;
        $office_withdraw = BazistWallet::whereDate('created_at', $date)->whereIn('user_id',[65332,64602])->where('method','برداشت')->sum('amount')/10;

        $rewards_first_bazist = BazistWallet::whereDate('created_at', $date)->where('type','first_submit_user')->sum('amount');
        $rewards_first_asan = AsanPardakht::whereDate('created_at', $date)->where('type','first_submit_user')->sum('amount');
        $rewards_first = $rewards_first_bazist+$rewards_first_asan;
        $rewards_first = $rewards_first > 0 ? $rewards_first/10 : $rewards_first;

        $rewards_ref_bazist = BazistWallet::whereDate('created_at', $date)->where('details','LIKE','%پاداش معرف%')->whereIn('type',['submit_user_ref','deposit'])->sum('amount');
        $rewards_ref_asan = AsanPardakht::whereDate('created_at', $date)->where('type','submit_user_ref')->sum('amount');
        $rewards_ref = $rewards_ref_bazist+$rewards_ref_asan;
        $rewards_ref = $rewards_ref > 0 ? $rewards_ref/10 : $rewards_ref;

//        $wallets_sum = Wallet::where('wallet', '!=', 0)->pluck('wallet')->whereDate('created_at', $date)->sum();
//        $cashout_sum = Cashout::whereIn('status', ['waiting', 'depositing'])->pluck('amount')->whereDate('created_at', $date)->sum();
//        $bazist_wallet = $wallets_sum+$cashout_sum;
        $wallets_sum = Wallet::where('wallet', '!=', 0)->pluck('wallet')->sum();
        $cashout_sum = Cashout::whereIn('status', ['waiting', 'depositing'])->pluck('amount')->sum();
        $bazist_wallet = $wallets_sum+$cashout_sum;

//        $bazist_wallet = DB::selectOne("
//    SELECT SUM(wallet_balance) AS total
//    FROM (
//        SELECT w.id, w.user_id, w.wallet_balance
//        FROM (
//            SELECT wallet_id, MAX(id) AS latest_id
//            FROM wallet_details
//            WHERE DATE(created_at) < ?
//            GROUP BY wallet_id
//        ) AS ids
//        JOIN wallet_details AS w ON ids.latest_id = w.id
//    ) AS t
//", [$date]); // Bind the date parameter to avoid SQL injection
//
//// The result will be an object with the total sum
//        $bazist_wallet = $bazist_wallet->total/10;
        return view('livewire.dashboard.stats.stat-total-cost-index-list',compact('app_total', 'app_withdraw', 'aap_deposit', 'bazist_wallet', 'cashout','office_deposit','office_withdraw','rewards_first','rewards_ref'));
    }


    #[On('date')]
    public function date($date)
    {
        $this->date = $date;
    }

}
