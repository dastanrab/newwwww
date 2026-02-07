<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\City;
use App\Models\Driver;
use App\Models\ReceiveArchive;
use App\Models\Submit;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatDailyIndexList extends Component
{
    #[Url]
    public $date;
    private  $city_id;

    public function boot()
    {
        $this->city_id = session('city',1) ;
        if ($this->city_id == 0){
            $this->city_id=1;
        }
    }
    public function render()
    {
        $city = auth()->user()->cityId();
        $city =$this->city_id;
        $future = [];
        $today = (object)[];
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
            //$end_date = Carbon::instance($end_month->datetime());

            $end_date = Carbon::now()->subDay();
            $futureDate = Carbon::now()->addDays(2)->format('Y-m-d');
            $futureDateFa = verta()->instance($futureDate)->format('Y/m/d');
            $query = DB::select("SELECT
            (SELECT count(*) FROM submits WHERE city_id = $city AND DATE(start_deadline) = '$futureDate') as total,
            (SELECT count(*) FROM submits WHERE city_id = $city AND DATE(start_deadline) = '$futureDate' AND status = 3) as collected,
            0 as legalCollected,
            0 as notLegalCollected,
            (SELECT count(*) FROM submits WHERE city_id = $city AND DATE(start_deadline) = '$futureDate' AND status IN(1,2)) as notCollected,
            (SELECT count(*) FROM submits WHERE city_id = $city AND DATE(start_deadline) = '$futureDate' AND status = 4) as canceledOperator,
          (SELECT count(*) FROM submits WHERE city_id = $city AND DATE(start_deadline) = '$futureDate' AND status = 5) as canceledUser
            ");
            $future[$futureDateFa] = $query;
            $futureDate = Carbon::now()->addDays(1)->format('Y-m-d');
            $futureDateFa = verta()->instance($futureDate)->format('Y/m/d');
            $query = DB::select("SELECT
            (SELECT count(*) FROM submits WHERE city_id = $city AND DATE(start_deadline) = '$futureDate') as total,
            0 as collected,
            0 as legalCollected,
            0 as notLegalCollected,
            (SELECT count(*) FROM submits WHERE city_id = $city AND DATE(start_deadline) = '$futureDate' AND status = 4) as canceledOperator,
          (SELECT count(*) FROM submits WHERE city_id = $city AND DATE(start_deadline) = '$futureDate' AND status = 5) as canceledUser
            ");
            $future[$futureDateFa] = $query;

            /******************** Today Stat **********************/
            $now = now()->format('Y-m-d');
            $total = DB::select("SELECT
    (SELECT count(*) FROM `submits` where DATE(start_deadline) = '$now' AND city_id = $city) as total,
    (SELECT count(*) FROM `submits` WHERE status = 4 AND DATE(start_deadline) = '$now' AND city_id = $city) as canceled_operator,
    (SELECT count(*) FROM `submits` WHERE status = 5 AND DATE(start_deadline) = '$now' AND city_id = $city) as canceled_user");
            $legal_collects_counts=DB::select("SELECT u.legal ,count(*) as count  from submits as s join users as u on s.user_id = u.id WHERE   s.`status` = 3 AND DATE(s.start_deadline) = '$now' AND s.city_id = $city GROUP BY u.legal");
            $legalCollected=0;
            $notLegalCollected=0;
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
            $today->date = verta()->format('Y/m/d');
            $weights=DB::table('drivers')
                ->join('submits', 'drivers.submit_id', '=', 'submits.id')
                ->join('users', 'users.id', '=', 'submits.user_id')
                ->select('users.legal', DB::raw('SUM(drivers.weights) as weight'))
                ->where('drivers.status', 3)
                ->where('submits.city_id', $this->city_id)
                ->whereDate('submits.start_deadline', $now)
                ->groupBy('users.legal')
                ->get();
            $legalWeight=0;
            $ilegalWeight=0;
            foreach ($weights as $weight)
            {
                if ($weight->legal == 1)
                {
                    $legalWeight=$weight->weight;
                }
                else{
                    $ilegalWeight=$weight->weight;
                }
            }
            $results = DB::table('drivers')
                ->join('submits', 'drivers.submit_id', '=', 'submits.id')
                ->select('submits.submit_phone',
                    DB::raw('SUM(drivers.weights) as weights'),
                    DB::raw('SUM(final_amount) as final_amount'),
                    DB::raw('SUM(total_amount) as total_amount'))
                ->where('drivers.status', 3)
                ->where('submits.city_id', $this->city_id)
                ->whereDate('submits.start_deadline', $now)
                ->groupBy('submits.submit_phone')
                ->get();
            $totalWeightApp=0;
            $totalAmount=0;
            $totalFinal=0;
            $phoneAmount=0;
            foreach ($results as $result)
            {
                if ($result->submit_phone == 1)
                {
                    $phoneAmount=$result->total_amount;

                }
                if ($result->submit_phone == 0)
                {
                    $totalWeightApp = $result->weights;
                }
                $totalAmount+=$result->total_amount;
                $totalFinal+=$result->final_amount;
            }
            $today->totalSubmit = $total[0]->total;
            $today->collectedSubmit = $legalCollected+$notLegalCollected;
            $today->legalCollectedSubmit = $legalCollected;
            $today->notLegalCollectedSubmit = $notLegalCollected;
            $today->notCollectedSubmit = $total[0]->total-$legalCollected-$notLegalCollected-$total[0]->canceled_operator-$total[0]->canceled_user;
            $today->canceledOperator = $total[0]->canceled_operator;
            $today->canceledUser = $total[0]->canceled_user;
            $today->totalWeight = $legalWeight + $ilegalWeight;
            $today->totalWeightLegal = $legalWeight;
            $today->totalWeightNotLegal = $ilegalWeight;
            $today->totalWeightApp = $totalWeightApp;
            $today->totalWeightTel = $today->totalWeight-$today->totalWeightApp;
//            $today->totalWeight = Driver::where('status',3)->whereDate('collected_at',$now)->sum('weights');
//            $today->totalWeightLegal = DB::table('drivers')
//                ->join('submits', 'drivers.submit_id', '=', 'submits.id')
//                ->join('users', 'users.id', '=', 'submits.user_id')
//                ->where('users.legal',1)
//                ->where('drivers.status',3)
//                ->whereDate('submits.start_deadline',$now)
//                ->sum('drivers.weights');

            $today->totalAmount = $totalAmount;
            $today->finalAmount = $totalFinal;
            $today->totalAmountPhone = $phoneAmount;
            $today->totalAmountApp = $today->totalAmount-$today->totalAmountPhone;

        }

        /******************** Archive Stat **********************/
        $submits = ReceiveArchive::where('type', 1)
            ->when($city, function ($query, $city) {
                return $query->where('city_id', $city);
            })
            ->whereBetween('date', [$start_date, $end_date])
            ->with(['archiveLegal', 'archiveNotLegal', 'archiveApp'])->orderBy('date', 'desc')->get();

        return view('livewire.dashboard.stats.stat-daily-index-list',compact('submits','future', 'today'));
    }

    #[On('date')]
    public function date($date)
    {
        $this->date = $date;
    }
    #[On('city')]
    public function city($city)
    {
        $this->city_id = session('city',1) ;
        if ($this->city_id == 0){
            $this->city_id=1;
        }
    }

}
