<?php

namespace App\Livewire\Dashboard\Home;

use App\Models\Hour;
use App\Models\Polygon;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class ChartsManagerList extends Component
{

    #[Url]
    public $date = 0;
    #[Url]
    public $type = 0;

    public $stats;
    public array $data;
    public $dateFrom;

    public function mount()
    {
        if ($this->type == 0 or $this->type == 1)
        {
            $this->data=$this->SubmitStats();
        }
        elseif ($this->type == 2){
            $this->data=$this->TonajStats();
        }
        elseif ($this->type == 3){
            $this->data=$this->RegionStats();
        }
        else{
            $this->data=$this->CancelStats();
        }

    }

    public function render()
    {
        return view('livewire.dashboard.home.charts-manager-list');
    }
    public function SubmitStats()
    {
        $hours=Hour::query()->get();
        $submits = DB::table('submits')
            ->select(['status',DB::raw('hour(start_deadline) as time_range ,count(*) as count')]);
        if (isset($this->date) and $this->date > 1)
        {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now();
            $submits=$submits->whereBetween('start_deadline',[$startOfMonth,$endOfMonth]);
        }
        else{
            $startDate = Carbon::now()->format('Y-m-d');
            $submits=$submits->whereDate('start_deadline', $startDate);
        }
        $submits=$submits->whereNotNull('start_deadline')->groupBy('time_range','status')->get();
        $data=[];
        foreach ($submits as $submit)
        {
            $start_hour=$submit->time_range;
            if (isset($data[$start_hour]))
            {
                $data[$start_hour]['total']+=$submit->count;

            }
            else{
                $data[$start_hour]=[];
                $data[$start_hour]['total']=$submit->count;
                $data[$start_hour]['collected']= $submit->status == 3 ? $submit->count:0;
                $data[$start_hour]['cancel']= $submit->status == 4 ? $submit->count:0;

            }
            if ($submit->status == 3 )
            {
                $data[$start_hour]['collected']= $submit->count;
            }
            if ($submit->status == 4 )
            {
                $data[$start_hour]['cancel']= $submit->count;
            }
        }
        $stats = [];
        foreach ($hours as $hour)
        {
            if (isset($data[$hour->start_at]))
            {
                $deliveredPercentage = ($data[$hour->start_at]['total'] > 0 and $data[$hour->start_at]['collected']) > 0 ? ($data[$hour->start_at]['collected'] / $data[$hour->start_at]['total']) * 100 : 0;
                $cancelPercentage = ($data[$hour->start_at]['total'] > 0 and $data[$hour->start_at]['cancel']) > 0 ? ($data[$hour->start_at]['cancel'] / $data[$hour->start_at]['total']) * 100 : 0;
                $notDeliveredPercentage = 100 - $deliveredPercentage - $cancelPercentage ;
                $deliveredCount=$data[$hour->start_at]['collected'];
                $cancelCount=$data[$hour->start_at]['cancel'];
                $notDeliveredCount=$data[$hour->start_at]['total'] - $data[$hour->start_at]['collected'] - $data[$hour->start_at]['cancel'];
                $stats[] = [
                    'date' => $hour->start_at.'-'.$hour->end_at,
                    'delivered'  => [
                'count' => $deliveredCount,
                'percentage' => $deliveredPercentage
            ],
                    'not_delivered' => [
                'count' => $notDeliveredCount,
                'percentage' => $notDeliveredPercentage
            ],
                    'cancel'=> [
                        'count' => $cancelCount,
                        'percentage' => $cancelPercentage
                    ]
                ];
            }
            else{
                $stats[] = [
                    'date' => $hour->start_at.'-'.$hour->end_at,
                    'delivered' => 0,
                    'not_delivered' => 0,
                    'cancel'=>0
                ];
            }
        }
        return $stats;

    }


    #[On('date')]
    public function date($date)
    {
        $this->date = $date;
        if ($this->type == 0 or $this->type == 1)
        {
            $this->data=$this->SubmitStats();
        }
        elseif ($this->type == 2){
            $this->data=$this->TonajStats();
        }
        elseif ($this->type == 3){
            $this->data=$this->RegionStats();
        }
        else{
            $this->data=$this->CancelStats();
        }
        $result['data']=$this->data;
        $result['type']=$this->type;
        $result['date']=$this->date;
        $this->dispatch('chartChanged', json_encode($result));
    }
    #[On('dateFrom')]
    public function dateFrom($date)
    {
        $this->dateFrom = $date;
        if ($this->type == 0 or $this->type == 1)
        {
            $this->data=$this->SubmitStats();
        }
        elseif ($this->type == 2){
            $this->data=$this->TonajStats();
        }
        elseif ($this->type == 3){
            $this->data=$this->RegionStats();
        }
        else{
            $this->data=$this->CancelStats();
        }
        $result['data']=$this->data;
        $result['type']=$this->type;
        $result['date']=$this->date;
        $this->dispatch('chartChanged', json_encode($result));
    }
    #[On('type')]
    public function type($type)
    {
        $this->type = $type;
        if ($this->type == 0 or $this->type == 1)
        {
            $this->data=$this->SubmitStats();
        }
        elseif ($this->type == 2){
            $this->data=$this->TonajStats();
        }
        elseif ($this->type == 3){
            $this->data=$this->RegionStats();
        }
        else{
            $this->data=$this->CancelStats();
        }
        $result['data']=$this->data;
        $result['type']=$this->type;
        $result['date']=$this->date;
        $this->dispatch('chartChanged', json_encode($result));
    }

    public function TonajStats()
    {
        $weights=DB::table('drivers')
            ->join('submits', 'drivers.submit_id', '=', 'submits.id')
            ->join('users', 'users.id', '=', 'submits.user_id')
            ->where('drivers.status', 3);
        if ($this->date == 0 or $this->date == 1)
        {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $weights=$weights->whereBetween('submits.start_deadline',[$startOfMonth,$endOfMonth])->groupBy('users.legal','date')->select('users.legal', DB::raw('SUM(drivers.weights) as weight ,date(submits.start_deadline) as date'));
        }
        else{
            $startOfYear = Carbon::now()->startOfYear();;
            $endOfYear = Carbon::now();
            $weights=$weights->whereBetween('submits.start_deadline',[$startOfYear,$endOfYear])->groupBy('users.legal','date')->select('users.legal', DB::raw("SUM(drivers.weights) as weight ,DATE_FORMAT(submits.start_deadline, '%Y-%m') as date "));
        }
            $weights=$weights->get();

        foreach ($weights as $weight)
        {
            $date=$weight->date;
            if (isset($data[$date]))
            {
                if ($weight->legal == 1  )
                {
                    $data[$date]['legal']=$weight->weight;
                }
                if ($weight->legal == 0 )
                {
                    $data[$date]['illegal']=$weight->weight;
                }

            }
            else{
                $data[$date]=[];
                $data[$date]['date']=$weight->date;
                $data[$date]['legal']=$weight->legal == 1 ? $weight->weight:0;
                $data[$date]['illegal']=$weight->legal == 0 ? $weight->weight:0;

            }

        }
        return array_values($data);
    }
    public function RegionStats()
    {
        $regions=Polygon::query()->select(['id','region'])->get();
        $region_submits=DB::table('submits')
            ->join('polygons','submits.region_id', '=', 'polygons.id')
            ->select('polygons.id','submits.status', DB::raw(" count(*) as count  ,polygons.region"));
        if ($this->date == 0 or $this->date == 1)
        {
            $today = Carbon::now();
            $region_submits=$region_submits->whereDate('submits.start_deadline',$today);
        }
        else{
            $startOfMonth = Carbon::now()->subMonth();
            $endOfMonth = Carbon::now();
            $region_submits=$region_submits->whereBetween('submits.created_at',[$startOfMonth,$endOfMonth]);
        }
        $region_submits=$region_submits->groupBy('submits.status','polygons.id')->get();
        $data=[];
        foreach ($region_submits as $submit)
        {
            $polygon_id=$submit->id;
            if (isset($data[$polygon_id]))
            {
                $data[$polygon_id]['total']+=$submit->count;

            }
            else{
                $data[$polygon_id]=[];
                $data[$polygon_id]['total']=$submit->count;
                $data[$polygon_id]['collected']= $submit->status == 3 ? $submit->count:0;
                $data[$polygon_id]['cancel']= $submit->status == 4 ? $submit->count:0;
                $data[$polygon_id]['driver_accept']= $submit->status == 2 ? $submit->count:0;

            }
            if ($submit->status == 3 )
            {
                $data[$polygon_id]['collected']= $submit->count;
            }
            if ($submit->status == 4 )
            {
                $data[$polygon_id]['cancel']= $submit->count;
            }
            if ($submit->status == 2 )
            {
                $data[$polygon_id]['driver_accept']= $submit->count;
            }
        }
        $stats = [];
        foreach ($regions as $region)
        {
            if (isset($data[$region->id]))
            {
                $deliveredPercentage = ($data[$region->id]['total'] > 0 and $data[$region->id]['collected']) > 0 ? ($data[$region->id]['collected'] / $data[$region->id]['total']) * 100 : 0;
                $cancelPercentage = ($data[$region->id]['total'] > 0 and $data[$region->id]['cancel']) > 0 ? ($data[$region->id]['cancel'] / $data[$region->id]['total']) * 100 : 0;
                $driverAcceptPercentage = ($data[$region->id]['total'] > 0 and $data[$region->id]['driver_accept']) > 0 ? ($data[$region->id]['driver_accept'] / $data[$region->id]['total']) * 100 : 0;
                $notDeliveredPercentage = 100 - $deliveredPercentage - $cancelPercentage ;
                $deliveredCount=$data[$region->id]['collected'];
                $cancelCount=$data[$region->id]['cancel'];
                $driverAcceptCount=$data[$region->id]['driver_accept'];
                $notDeliveredCount=$data[$region->id]['total'] - $data[$region->id]['collected'] - $data[$region->id]['cancel'] - $data[$region->id]['driver_accept'];
                $stats[] = [
                    'region' => $region->region,
                    'delivered'  => [
                        'count' => $deliveredCount,
                        'percentage' => $deliveredPercentage
                    ],
                    'not_delivered' => [
                        'count' => $notDeliveredCount,
                        'percentage' => $notDeliveredPercentage
                    ],
                    'cancel'=> [
                        'count' => $cancelCount,
                        'percentage' => $cancelPercentage
                    ],
                    'driver_accept'=>[
                        'count' => $driverAcceptCount,
                        'percentage' => $driverAcceptPercentage
                    ]
                ];
            }
            else{
                $stats[] = [
                    'region' => $region->region,
                    'delivered'  => [
                        'count' => 0,
                        'percentage' => 0
                    ],
                    'not_delivered' => [
                        'count' => 0,
                        'percentage' => 0
                    ],
                    'cancel'=> [
                        'count' =>0,
                        'percentage' => 0
                    ],
                    'driver_accept'=>[
                        'count' => 0,
                        'percentage' => 0
                    ]
                ];
            }
        }
        return $stats;
    }
    public function CancelStats()
    {
        $cancels=['لغو شده توسط ادمین'=>['count'=>0],'سایر'=>['count'=>0],'نامناسب بودن وضعیت پسماند'=>['count'=>0],'عدم پاسخگویی شهروند در زمان مراجعه'=>['count'=>0],
            'موکول به زمان دیگر'=>['count'=>0],'مسدود بودن آدرس'=>['count'=>0],'اشتباه راننده در ثبت اطلاعات'=>['count'=>0],'سایر موارد'=>['count'=>0],'خرابی خودرو'=>['count'=>0]];
        $cancelCounts = DB::table('submits')
            ->select('cancel', DB::raw('COUNT(1) as count'))
            ->whereNotNull('cancel');
        if (isset($this->dateFrom)){
            $from = toGregorian($this->dateFrom,'/','-',false);
            $cancelCounts=$cancelCounts->whereDate('submits.start_deadline',$from);
        }
        elseif ($this->date == 0 or $this->date == 1)
        {
            $today = Carbon::now();
            $cancelCounts=$cancelCounts->whereDate('submits.start_deadline',$today);
        }
        else{
            $startOfMonth = Carbon::now()->subMonth();
            $endOfMonth = Carbon::now();
            $cancelCounts=$cancelCounts->whereBetween('submits.created_at',[$startOfMonth,$endOfMonth]);
        }
        $cancelCounts=$cancelCounts->groupBy('cancel')->get()->keyBy('cancel')->toArray();
        $stats = [];
        foreach ($cancels as $key => $cancel)
        {
            if (isset($cancelCounts[$key]))
            {
                $stats[] = [
                    'reason' => $key,
                    'count'=>$cancelCounts[$key]->count
                ];
            }
            else{
                $stats[] = [
                    'reason' => $key,
                    'count'=>0
                ];
            }
        }
        return $stats;
    }
}
