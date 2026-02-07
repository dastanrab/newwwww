<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\ArchiveApp;
use App\Models\ArchiveLegal;
use App\Models\ArchiveNotLegal;
use App\Models\Driver;
use App\Models\ReceiveArchive;
use App\Models\Submit;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatDailyIndexListBackup extends Component
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

            $firstDayOfMonth = Verta::parse($this->date);
            // تعداد روزهای آن ماه را محاسبه می‌کنیم
            $daysInMonth = $firstDayOfMonth->daysInMonth;
            // یک آرایه برای ذخیره روزها ایجاد می‌کنیم
            $dateRanges = [];
            // روزها را به آرایه اضافه می‌کنیم
            for ($i = 0; $i < $daysInMonth; $i++) {
                $dateRanges[] = Verta::parse($this->date)->addDays($i)->format('Y-m-d');
            }

        }
        else {

            $fa_date = verta();
            $start_month = $fa_date->startMonth();
            $start_month = Verta::instance($start_month);
            $start_date = Carbon::instance($start_month->datetime());
            $end_month = $fa_date->endMonth();
            $end_month = Verta::instance($end_month);
            $end_date = Carbon::instance($end_month->datetime());


            $firstDayOfMonth = Verta::instance($start_date);
            $currentDate = Verta::now();
            $dateRanges = [];
            for ($date = $firstDayOfMonth; $date <= $currentDate; $date->addDay()) {
                $dateRanges[] = $date->format('Y-m-d');
            }

        }




        $submits = ReceiveArchive::where('type', 1)
            ->when($city, function ($query, $city) {
                return $query->where('city_id', $city);
            })
            ->whereBetween('date', [$start_date, $end_date])
            ->with(['archiveLegal', 'archiveNotLegal', 'archiveApp'])->orderBy('date', 'desc')->get();







        //$total_count = App\Models\ReceiveArchive::where('city_id', auth()->user()->cityId())->pluck('submit_count')->sum();
        $total_count = Submit::where('city_id', auth()->user()->cityId())->count();
        $total_done_count = Submit::where('city_id', auth()->user()->cityId())->where('status',3)->count();
        $total_not_collected_count = Submit::where('city_id', auth()->user()->cityId())->whereIn('status',[1,2])->count();
        $total_cancel_count = Submit::where('city_id', auth()->user()->cityId())->where('status',5)->count(); //توسط کاربر
        $total_delete_count = Submit::where('city_id', auth()->user()->cityId())->where('status',4)->count(); // توسط ادمین
        //$total_weight = App\Models\ReceiveArchive::where('city_id', auth()->user()->cityId())->pluck('weight')->sum();
        //$total_weight = Receive::all()->sum('weight'); // خیلی دقیق تر
        $total_weight = Driver::all()->sum('weights');

        //$total_weight_legal = App\Models\ArchiveLegal::where('city_id', auth()->user()->cityId())->pluck('weight')->sum();
        $total_weight_legal = Driver::with('submit')->whereHas('submit',function ($q){
            $q->whereHas('user', function ($q){
                $q->where('legal',1);
            });
        })->pluck('weights')->sum();

        //$total_weight_not_legal = App\Models\ArchiveNotLegal::where('city_id', auth()->user()->cityId())->pluck('weight')->sum();
        $total_weight_not_legal = Driver::with('submit')->whereHas('submit',function ($q){
            $q->whereHas('user', function ($q){
                $q->where('legal',0);
            });
        })->pluck('weights')->sum();

        //$total_weight_app = ArchiveApp::where('city_id', auth()->user()->cityId())->pluck('weight')->sum();
        $total_weight_app = Driver::with('submit')->whereHas('submit',function ($q){
            $q->where('submit_phone',0);
        })->pluck('weights')->sum();


        //$total_weight_tel = ReceiveArchive::where('city_id', auth()->user()->cityId())->pluck('weight')->sum() - ArchiveApp::where('city_id', auth()->user()->cityId())->pluck('weight')->sum();
        $total_weight_tel = Driver::with('submit')->whereHas('submit',function ($q){
            $q->where('submit_phone',1);
        })->pluck('weights')->sum();

        //$final_amount = App\Models\ReceiveArchive::where('city_id', auth()->user()->cityId())->pluck('value')->sum()/10;
        $final_amount = Submit::all()->sum('final_amount'); // all => 17,010,205,646
        $total_amount_app = Submit::where('submit_phone',0)->sum('total_amount'); // pay for user
        $total_amount_tel = Submit::where('submit_phone',1)->sum('total_amount'); // pay for user
        $fava_share = (ArchiveLegal::where('city_id', auth()->user()->cityId())->pluck('fava_pay_share')->sum() + ArchiveNotLegal::where('city_id', auth()->user()->cityId())->pluck('fava_pay_share')->sum())/10;
        $fava_paid = ReceiveArchive::where('city_id', auth()->user()->cityId())->pluck('fava_pay')->sum()/10;

        /*********************************************************************************************************/
        $total_month_count = Submit::where('city_id', auth()->user()->cityId())->whereBetween('start_deadline', [$start_date,$end_date])->count();
        $total_month_done_count = Driver::where('city_id', auth()->user()->cityId())->whereBetween('collected_at', [$start_date,$end_date])->where('status',3)->count();
        $total_month_not_collected_count = Submit::where('city_id', auth()->user()->cityId())->whereBetween('start_deadline', [$start_date,$end_date])->whereIn('status',[1,2])->count();
        $total_month_cancel_count = Submit::where('city_id', auth()->user()->cityId())->whereBetween('start_deadline', [$start_date,$end_date])->where('status',5)->count(); //توسط کاربر
        $total_month_delete_count = Submit::where('city_id', auth()->user()->cityId())->whereBetween('start_deadline', [$start_date,$end_date])->where('status',4)->count(); // توسط ادمین
        //$month_weight = $weight_month = $submits->pluck('weight')->sum();
        $month_weight = Driver::where('city_id', auth()->user()->cityId())->whereBetween('collected_at', [$start_date,$end_date])->sum('weights');

        //$month_weight_legal = App\Models\ArchiveLegal::where('city_id', auth()->user()->cityId())->whereBetween('date', [$start_date, $end_date])->pluck('weight')->sum()
        $month_weight_legal = Driver::with('submit')->where('city_id', auth()->user()->cityId())->whereBetween('collected_at', [$start_date,$end_date])->whereHas('submit',function ($q){
            $q->whereHas('user', function ($q){
                $q->where('legal',1);
            });
        })->pluck('weights')->sum();

        $month_weight_not_legal = Driver::with('submit')->where('city_id', auth()->user()->cityId())->whereBetween('collected_at', [$start_date,$end_date])->whereHas('submit',function ($q){
            $q->whereHas('user', function ($q){
                $q->where('legal',0);
            });
        })->pluck('weights')->sum();

        //$weight_month_archive_app = App\Models\ArchiveApp::where('city_id', auth()->user()->cityId())->whereBetween('date', [$start_date, $end_date])->pluck('weight');
        $weight_month_app = Driver::with('submit')->where('city_id', auth()->user()->cityId())->whereBetween('collected_at', [$start_date,$end_date])->whereHas('submit',function ($q){
        $q->where('submit_phone',0);
    })->pluck('weights')->sum();

        $final_month_amount = Submit::where('city_id', auth()->user()->cityId())->whereBetween('start_deadline', [$start_date,$end_date])->sum('final_amount'); // all
        $total_month_amount_app = Submit::where('city_id', auth()->user()->cityId())->whereBetween('start_deadline', [$start_date,$end_date])->where('submit_phone',0)->sum('total_amount'); // pay for user
        $total_month_amount_tel = Submit::where('city_id', auth()->user()->cityId())->whereBetween('start_deadline', [$start_date,$end_date])->where('submit_phone',1)->sum('total_amount'); // pay for user
        $fava_month_pay = $submits->pluck('fava_pay')->sum()/10;

        /**************************************************************/
        $sql = "SELECT ";
        $i = 0;
        foreach ($dateRanges as $dateRange){
            $date = Verta::parse($dateRange)->toCarbon()->format('Y-m-d');
            $sql .= " (SELECT count(*) FROM submits WHERE city_id = '".auth()->user()->cityId()."' AND start_deadline BETWEEN '$date 00:00:00' AND '$date 23:59:59') AS key_{$i},";
            $i++;
        }
        $sql = substr_replace($sql, '', -1);
        $all_total = DB::SELECT($sql);
        /**************************************************************/
        $sql = "SELECT ";
        $i = 0;
        foreach ($dateRanges as $dateRange){
            $date = Verta::parse($dateRange)->toCarbon()->format('Y-m-d');
            $sql .= " (SELECT count(*) FROM submits WHERE status = 3 AND city_id = '".auth()->user()->cityId()."' AND start_deadline BETWEEN '$date 00:00:00' AND '$date 23:59:59') AS key_{$i},";
            $i++;
        }
        $sql = substr_replace($sql, '', -1);
        $all_total_done = DB::SELECT($sql);
        /**************************************************************/
        $sql = "SELECT ";
        $i = 0;
        foreach ($dateRanges as $dateRange){
            $date = Verta::parse($dateRange)->toCarbon()->format('Y-m-d');
            $sql .= " (SELECT count(*) FROM submits WHERE status IN(1,2) AND city_id = '".auth()->user()->cityId()."' AND start_deadline BETWEEN '$date 00:00:00' AND '$date 23:59:59') AS key_{$i},";
            $i++;
        }
        $sql = substr_replace($sql, '', -1);
        $all_total_not_collected = DB::SELECT($sql);
        /**************************************************************/
        $sql = "SELECT ";
        $i = 0;
        foreach ($dateRanges as $dateRange){
            $date = Verta::parse($dateRange)->toCarbon()->format('Y-m-d');
            $sql .= " (SELECT count(*) FROM submits WHERE status = 5 AND city_id = '".auth()->user()->cityId()."' AND start_deadline BETWEEN '$date 00:00:00' AND '$date 23:59:59') AS key_{$i},";
            $i++;
        }
        $sql = substr_replace($sql, '', -1);
        $all_total_cancel = DB::SELECT($sql);
        /**************************************************************/

        $sql = "SELECT ";
        $i = 0;
        foreach ($dateRanges as $dateRange){
            $date = Verta::parse($dateRange)->toCarbon()->format('Y-m-d');
            $sql .= " (SELECT count(*) FROM submits WHERE status = 4 AND city_id = '".auth()->user()->cityId()."' AND start_deadline BETWEEN '$date 00:00:00' AND '$date 23:59:59') AS key_{$i},";
            $i++;
        }
        $sql = substr_replace($sql, '', -1);
        $all_total_delete = DB::SELECT($sql);
        /**************************************************************/
        $sql = "SELECT ";
        $i = 0;
        foreach ($dateRanges as $dateRange){
            $date = Verta::parse($dateRange)->toCarbon()->format('Y-m-d');
            $sql .= " (SELECT sum(weights) FROM drivers WHERE city_id = '".auth()->user()->cityId()."' AND collected_at BETWEEN '$date 00:00:00' AND '$date 23:59:59') AS key_{$i},";
            $i++;
        }
        $sql = substr_replace($sql, '', -1);
        $all_total_weight = DB::SELECT($sql);
        /**************************************************************/
        $sql = "SELECT ";
        $i = 0;
        foreach ($dateRanges as $dateRange){
            $date = Verta::parse($dateRange)->toCarbon()->format('Y-m-d');
            $sql .= " (SELECT sum(total_amount) FROM submits WHERE city_id = '".auth()->user()->cityId()."' AND collected_at BETWEEN '$date 00:00:00' AND '$date 23:59:59') AS key_{$i},";
            $i++;
        }
        $sql = substr_replace($sql, '', -1);
        $all_total_user_pay = DB::SELECT($sql);
        /**************************************************************/

        $sql = "SELECT ";
        $i = 0;
        foreach ($dateRanges as $dateRange){
            $date = Verta::parse($dateRange)->toCarbon()->format('Y-m-d');
            $sql .= " (SELECT sum(final_amount) FROM submits WHERE city_id = '".auth()->user()->cityId()."' AND collected_at BETWEEN '$date 00:00:00' AND '$date 23:59:59') AS key_{$i},";
            $i++;
        }
        $sql = substr_replace($sql, '', -1);
        $all_total_amount = DB::SELECT($sql);
        /**************************************************************/

        $sql = "SELECT ";
        $i = 0;
        foreach ($dateRanges as $dateRange){
            $date = Verta::parse($dateRange)->toCarbon()->format('Y-m-d');
            $sql .= " (SELECT sum(final_amount) FROM submits WHERE city_id = '".auth()->user()->cityId()."' AND collected_at BETWEEN '$date 00:00:00' AND '$date 23:59:59') AS key_{$i},";
            $i++;
        }
        $sql = substr_replace($sql, '', -1);
        $all_total_amount = DB::SELECT($sql);

        $total_weight_legal = Driver::with('submit')->whereHas('submit',function ($q){
            $q->whereHas('user', function ($q){
                $q->where('legal',1);
            });
        })->pluck('weights')->sum();
        /**************************************************************/

        return view('livewire.dashboard.stats.stat-daily-index-list',
            compact('submits', 'start_date', 'end_date', 'total_count', 'total_done_count', 'total_not_collected_count', 'total_cancel_count', 'total_delete_count', 'total_weight', 'total_weight_legal', 'total_weight_not_legal', 'total_weight_app', 'total_weight_tel', 'final_amount', 'total_amount_app','total_amount_tel','fava_share','fava_paid','total_month_count','total_month_done_count','total_month_not_collected_count','total_month_cancel_count','total_month_delete_count','month_weight', 'month_weight_legal', 'month_weight_not_legal','weight_month_app','final_month_amount','total_month_amount_app','total_month_amount_tel','fava_month_pay', 'dateRanges', 'all_total', 'all_total_done', 'all_total_not_collected', 'all_total_cancel', 'all_total_delete', 'all_total_weight', 'all_total_user_pay', 'all_total_amount')
        );
    }

    #[On('date')]
    public function date($date)
    {
        $this->date = $date;
    }

}
