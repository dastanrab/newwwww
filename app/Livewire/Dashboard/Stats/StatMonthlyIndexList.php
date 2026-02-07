<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\Driver;
use App\Models\Submit;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatMonthlyIndexList extends Component
{

    #[Url]
    public $date;
    public function render()
    {
        $city = auth()->user()->cityId();
        if($this->date) {
            $start_month = Verta::parse($this->date);
            $start_date = Carbon::instance($start_month->datetime());
            $end_month = $start_month->endMonth();
            $end_date = Carbon::instance($end_month->datetime());
        }
        else {
            $fa_date = verta();
            $start_month = Verta::instance($fa_date->startMonth());
            $start_date = Carbon::instance($start_month->datetime());
            $end_month = Verta::instance($fa_date->endMonth());
            $end_date = Carbon::instance($end_month->datetime());
        }
//        month vaariz
        $result = DB::select("SELECT get_vaariz_sums(?, ?) AS sums", [$start_date, $end_date]);
        $sums = json_decode($result[0]->sums, true);
        $card_vaariz_sum = $sums['card_vaariz_sum'] ?? 0;
        $asan_pardakht_vaariz_sum = $sums['asan_pardakht_vaariz_sum'] ?? 0;
        $user_cashout_sum = $sums['user_cashout_sum'] ?? 0;
        $admin_cashout_sum = $sums['admin_cashout_sum'] ?? 0;
        $return_from_user_wallet_sum = $sums['return_from_user_wallet_sum'] ?? 0;
        $user_aap_cashout_sum = $sums['user_aap_cashout_sum'] ?? 0;
        $sharj_vaariz_sum = $sums['sharj_vaariz_sum'] ?? 0;
        $total_sum = $card_vaariz_sum  + $user_cashout_sum + $admin_cashout_sum ;
        /****************** month stat *******************/
        $total = DB::select("SELECT
    (SELECT count(*) FROM `submits` where DATE(start_deadline) between '$start_date' AND '$end_date') as total,
    (SELECT count(*) FROM `submits` WHERE status = 3 AND DATE(start_deadline) between '$start_date' AND '$end_date') as collected,
    (SELECT count(*) FROM `submits` WHERE status IN (1,2) AND city_id = 1 AND DATE(start_deadline) between '$start_date' AND '$end_date') as not_collected,
    (SELECT count(*) FROM `submits` WHERE status = 4 AND DATE(start_deadline) between '$start_date' AND '$end_date') as canceledOperator,
    (SELECT count(*) FROM `submits` WHERE status = 5 AND DATE(start_deadline) between '$start_date' AND '$end_date') as canceledUser");
        $legal_collects_counts=DB::select("SELECT u.legal ,count(*) as count  from submits as s join users as u on s.user_id = u.id WHERE   s.`status` = 3 AND DATE(start_deadline) between '$start_date' AND '$end_date' GROUP BY u.legal");
        foreach ($legal_collects_counts as $legal_collects_count )
        {
            if ($legal_collects_count->legal == 1)
            {
                $legalCollected = $legal_collects_count->count;
            }
            else{
                $notLegalCollected = $legal_collects_count->count;
            }
        }
        $monthLegalCollectedSubmit = $legalCollected??0;
        $monthNotLegalCollectedSubmit = $notLegalCollected??0;
        $monthTotalSubmit = $total[0]->total;
        $monthCollectedSubmit = $total[0]->collected;
        $monthNotCollectedSubmit = $total[0]->not_collected;
        $monthCanceledOperator = $total[0]->canceledOperator;
        $monthCanceledUser = $total[0]->canceledUser;
        $monthTotalWeight = Driver::where('status',3)->whereBetween('collected_at',[$start_date,$end_date])->sum('weights');
        $monthTotalWeightLegal = DB::table('drivers')
            ->join('submits', 'drivers.submit_id', '=', 'submits.id')
            ->join('users', 'users.id', '=', 'submits.user_id')
            ->where('users.legal',1)
            ->where('drivers.status',3)
            ->whereBetween('submits.start_deadline',[$start_date,$end_date])
            ->sum('drivers.weights');
        $monthTotalWeightNotLegal = $monthTotalWeight-$monthTotalWeightLegal;
        $monthTotalWeightApp = DB::table('drivers')
            ->join('submits', 'drivers.submit_id', '=', 'submits.id')
            ->where('submits.submit_phone',1)
            ->where('drivers.status',3)
            ->whereBetween('submits.start_deadline',[$start_date,$end_date])
            ->sum('drivers.weights');
        $monthTotalWeightTel = $monthTotalWeight-$monthTotalWeightApp;

        $total = Submit::where('status',3)
            ->whereBetween('start_deadline',[$start_date,$end_date])
            ->get([
                DB::raw('SUM(final_amount) as final_amount'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw("(SELECT SUM(total_amount) FROM submits WHERE submit_phone = 1 AND DATE(start_deadline) between '$start_date' AND '$end_date') as total_amount_phone"),
            ])->first();
        $monthTotalAmount = $total->total_amount;
        $monthFinalAmount = $total->final_amount;
        $monthTotalAmountPhone = $total->total_amount_phone;
        $monthTotalAmountApp = $monthTotalAmount-$monthTotalAmountPhone;

        return view('livewire.dashboard.stats.stat-monthly-index-list', compact('monthTotalSubmit','monthCollectedSubmit','monthNotCollectedSubmit','monthCanceledOperator','monthCanceledUser','monthTotalWeight','monthTotalWeightLegal','monthTotalWeightNotLegal','monthTotalWeightApp','monthTotalWeightTel','monthTotalAmount', 'monthFinalAmount', 'monthTotalAmountPhone', 'monthTotalAmountApp','monthLegalCollectedSubmit','monthNotLegalCollectedSubmit','asan_pardakht_vaariz_sum','total_sum','card_vaariz_sum','sharj_vaariz_sum','user_aap_cashout_sum','user_cashout_sum','admin_cashout_sum','return_from_user_wallet_sum'));
    }


    #[On('date')]
    public function date($date)
    {
        $this->date = $date;
    }
}
