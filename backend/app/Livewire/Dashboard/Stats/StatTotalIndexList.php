<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\Driver;
use App\Models\Submit;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StatTotalIndexList extends Component
{
    public function render()
    {

        /****************** total stat *******************/
        $total = Submit::where('status',3)->get([
            DB::raw('SUM(final_amount) as final_amount'),
            DB::raw('SUM(total_amount) as total_amount'),
            DB::raw('(SELECT SUM(total_amount) FROM submits WHERE submit_phone = 1) as total_amount_phone'),
        ])->first();
        $totalAmount = $total->total_amount;
        $finalAmount = $total->final_amount;
        $totalAmountPhone = $total->total_amount_phone;
        $totalAmountApp = $totalAmount-$totalAmountPhone;
        $total = DB::select("SELECT
    (SELECT count(*) FROM `submits`) as total,
    (SELECT count(*) FROM `submits` WHERE status = 3) as collected,
    (SELECT count(*) FROM `submits` INNER JOIN users ON submits.user_id = users.id WHERE submits.status = 3 AND users.legal = 1) as legal_collected,
    (SELECT count(*) FROM `submits` WHERE status = 3 AND submit_phone = 1) as phone_collected,
    (SELECT count(*) FROM `submits` WHERE status IN (1,2) AND city_id = 1) as not_collected,
    (SELECT count(*) FROM `submits` WHERE status = 4) as canceledOperator,
    (SELECT count(*) FROM `submits` WHERE status = 5) as canceledUser");
        $totalSubmit = $total[0]->total;
        $collectedSubmit = $total[0]->collected;
        $legalCollectedSubmit = $total[0]->legal_collected;
        $notLegalCollectedSubmit = $total[0]->collected-$total[0]->legal_collected;
        $phoneTotalSubmit = $total[0]->phone_collected;
        $appCollectedSubmit = $total[0]->collected-$total[0]->phone_collected;
        $notCollectedSubmit = $total[0]->not_collected;
        $canceledOperator = $total[0]->canceledOperator;
        $canceledUser = $total[0]->canceledUser;
        $totalWeight = Driver::where('status',3)->sum('weights');
        $totalWeightLegal = DB::table('drivers')
            ->join('submits', 'drivers.submit_id', '=', 'submits.id')
            ->join('users', 'users.id', '=', 'submits.user_id')
            ->where('users.legal',1)
            ->where('drivers.status',3)
            ->sum('drivers.weights');
        $totalWeightNotLegal = $totalWeight-$totalWeightLegal;
        $totalWeightApp = DB::table('drivers')
            ->join('submits', 'drivers.submit_id', '=', 'submits.id')
            ->where('submits.submit_phone',1)
            ->where('drivers.status',3)
            ->sum('drivers.weights');
        $totalWeightTel = $totalWeight-$totalWeightApp;

        return view('livewire.dashboard.stats.stat-total-index-list', compact('totalAmount', 'finalAmount' ,'totalAmountPhone', 'totalAmountApp', 'totalSubmit', 'collectedSubmit', 'legalCollectedSubmit', 'notLegalCollectedSubmit', 'phoneTotalSubmit', 'appCollectedSubmit', 'notCollectedSubmit', 'canceledOperator', 'canceledUser', 'totalWeight', 'totalWeightLegal', 'totalWeightNotLegal', 'totalWeightApp', 'totalWeightTel'));
    }
}
