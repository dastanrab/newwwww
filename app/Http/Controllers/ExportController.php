<?php

namespace App\Http\Controllers;

use App\Exports\DriversWastesWeightExport;
use App\Exports\StatisticsAdminDeposite;
use App\Exports\StatisticsCashoutExport;
use App\Exports\StatisticsCharityExport;
use App\Exports\StatisticsDailySubmits;
use App\Exports\StatisticsDivisionIndexExport;
use App\Exports\StatisticsDriverRollCallExport;
use App\Exports\StatisticsDriverSalaryExport;
use App\Exports\StatisticsFavaAll;
use App\Exports\StatisticsFavaMiss;
use App\Exports\StatisticsFavaSubmits;
use App\Exports\StatisticsFavaSum;
use App\Exports\StatisticsFinArchive2Export;
use App\Exports\StatisticsFinArchiveExport;
use App\Exports\StatisticsHotels;
use App\Exports\StatisticsInax;
use App\Exports\StatisticsIndexExport;
use App\Exports\StatisticsManualTransactionsExport;
use App\Exports\StatisticsMontlyFinExport;
use App\Exports\StatisticsReward;
use App\Exports\StatisticsSamanUnTrace;
use App\Exports\StatisticsSchools;
use App\Exports\StatisticsWarehouseDriverDetailExport;
use App\Exports\StatisticsWarehouseDriverExport;
use App\Exports\StatisticsWarehouseDriversWeightsExport;
use App\Exports\StatisticsWasteExport;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function statCharity()
    {

        $filename = 'آنیروب  - ' .  verta()->format('d-m-Y') . ' .xlsx';

        return Excel::download(new StatisticsCharityExport(), $filename);
    }
    public function statFavaMiss()
    {

        $filename = 'آنیروب فاوا - ' .  verta()->format('d-m-Y') . ' .xlsx';

        return Excel::download(new StatisticsFavaMiss(), $filename);
    }
    public function statFavaSum()
    {

        $filename = 'آنیروب فاوا - ' .  verta()->format('d-m-Y') . ' .xlsx';

        return Excel::download(new StatisticsFavaSum(), $filename);
    }
    public function statAdminDeposit()
    {

        $filename = 'آنیروب فاوا - ' .  verta()->format('d-m-Y') . ' .xlsx';

        return Excel::download(new StatisticsAdminDeposite(), $filename);
    }
    public function statInax()
    {

        $filename = 'آنیروب آینکس فاوا - ' .  verta()->format('d-m-Y') . ' .xlsx';

        return Excel::download(new StatisticsInax(), $filename);
    }
    public function statFavaSubmits()
    {

        $filename = 'آنیروب درخواست های ارسال شده فاوا - ' .  verta()->format('d-m-Y') . ' .xlsx';

        return Excel::download(new StatisticsFavaSubmits(), $filename);
    }
    public function statReward()
    {

        $filename = 'آنیروب پاداش - ' .  verta()->format('d-m-Y') . ' .xlsx';

        return Excel::download(new StatisticsReward(), $filename);
    }
    public function statHotels()
    {

        $filename = 'آنیروب پاداش - ' .  verta()->format('d-m-Y') . ' .xlsx';

        return Excel::download(new StatisticsHotels(), $filename);
    }
    public function statSchools()
    {

        $filename = 'آنیروب آمار مدارس - ' .  verta()->format('d-m-Y') . ' .xlsx';

        return Excel::download(new StatisticsSchools(), $filename);
    }
    public function statSamanUnTrace()
    {

        $filename = 'آنیروب بلاتکلیف سامان - ' .  verta()->format('d-m-Y') . ' .xlsx';

        return Excel::download(new StatisticsSamanUnTrace(), $filename);
    }
    public function statDailySubmits(Request $request)
    {

        $filename = 'آنیروب آمار روزانه - ' .  verta()->format('d-m-Y') . ' .xlsx';
        if(isset($request->date) and $request->date != 'undefined'){
            $start_month = Verta::parse($request->date);
            $start_date = Carbon::instance($start_month->datetime());
            $end_month = $start_month->endMonth();
            $end_date = Carbon::instance($end_month->datetime());
        }
        else{
            $fa_date = verta();
            $start_month = Verta::instance($fa_date->startMonth());
            $start_date = Carbon::instance($start_month->datetime());
            $end_date = Carbon::now()->subDay();
        }
        return Excel::download(new StatisticsDailySubmits($start_date,$end_date), $filename);
    }
    public function statDaily(Request $request)
    {
        $filename = 'آنیروب - آمار کلی - ' .  verta()->format('d-m-Y') . ' .xlsx';
        $dateFrom = '';
        $dateTo = '';
        $status = $request->status;
        $driver_id=$request->driver_id;
        $search=$request->search;
        $type=$request->type;
        if($request->dateFrom and $request->dateFrom != 'undefined'){
            $dateFrom = Verta::parse($request->dateFrom)->toCarbon()->format('Y-m-d 00:00:00');
            $dateTo = Verta::parse($request->dateTo)->toCarbon()->format('Y-m-d 23:59:59');
        }

        return Excel::download(new StatisticsIndexExport($dateFrom, $dateTo,$status,$driver_id,$search,$type), $filename);
        //return Excel::download(new StatisticsIndexExport('2024-05-21', '2024-06-20'), 'khordad-1403.xlsx');
    }
    public function statDailyDivision(Request $request)
    {
        $filename = 'آنیروب - آمار کلی - ' .  verta()->format('d-m-Y') . ' .xlsx';
        $dateFrom = '';
        $dateTo = '';
        $status = $request->status;
        $driver_id=$request->driver_id;
        $search=$request->search;
        $type=$request->type;
        if($request->dateFrom and $request->dateFrom != 'undefined'){
            $dateFrom = Verta::parse($request->dateFrom)->toCarbon()->format('Y-m-d 00:00:00');
            $dateTo = Verta::parse($request->dateTo)->toCarbon()->format('Y-m-d 23:59:59');
        }

        return Excel::download(new StatisticsDivisionIndexExport($dateFrom, $dateTo,$status,$driver_id,$search,$type), $filename);}
    public function warehouseDriver(Request $request)
    {
        $filename = 'آنیروب - آمار بار رانندگان - ' .  verta()->format('d-m-Y') . ' .xlsx';
        if(isset($request->date) and $request->date != 'undefined'){
            $date= Verta::parse($request->date)->toCarbon()->format('Y-m-d 00:00:00');
        }
        else{
            $date=now();
        }
        if (isset($request->date_to) and $request->date_to != 'undefined')
        {
            $dateTo = Verta::parse($request->date_to)->toCarbon()->format('Y-m-d 00:00:00');
            return Excel::download(new StatisticsWarehouseDriversWeightsExport($date,$dateTo), $filename);
        }
        return Excel::download(new StatisticsWarehouseDriverExport($date), $filename);
    }
    public function warehouseDriverDetail(Request $request)
    {
        $filename = 'آنیروب - آمار بار رانندگان - ' .  verta()->format('d-m-Y') . ' .xlsx';
        if(isset($request->date) and $request->date != 'undefined'){
            $date= Verta::parse($request->date)->toCarbon()->format('Y-m-d 00:00:00');
        }
        else{
            $date=now();
        }
        return Excel::download(new StatisticsWarehouseDriverDetailExport($date), $filename);
    }

    public function driverRollCall(Request $request)
    {
        $filename = 'آنیروب - آمار حضور غیاب - ' .  verta()->format('d-m-Y') . ' .xlsx';
        if($request->dateFrom && $request->dateTo && $request->user_id){
            $dateFrom = Verta::parse($request->dateFrom)->toCarbon()->format('Y-m-d 00:00:00');
            $dateTo = Verta::parse($request->dateTo)->toCarbon()->format('Y-m-d 23:59:59');
            return Excel::download(new StatisticsDriverRollCallExport($request->user_id,$dateFrom, $dateTo), $filename);
        }
    }
    public function driverSalary(Request $request)
    {
        $filename = 'آنیروب - آمار حقوق رانندگان - ' .  verta()->format('d-m-Y') . ' .xlsx';
        if($request->dateFrom && $request->dateFrom != 'undefined'){
            $dateFrom = Verta::parse($request->dateFrom)->toCarbon()->format('Y-m-d 00:00:00');
            return Excel::download(new StatisticsDriverSalaryExport($dateFrom), $filename);
        }
    }

    public function statWastes(Request $request)
    {
        if (isset($request->date))
        {
            $date=toGregorian($request->date,'/','-',false);
        $filename = 'waste.xlsx';
        return Excel::download(new StatisticsWasteExport($date), $filename);
        }

    }
    public function statMonthly()
    {
        $filename = 'MonthlyFin.xlsx';
        return Excel::download(new StatisticsMontlyFinExport(), $filename);
    }
    public function statManualTransactions(Request $request)
    {
        $filename = 'ManualTransactions.xlsx';
        return Excel::download(new StatisticsManualTransactionsExport(@$request->dateFrom), $filename);
    }
    public function statFinArchive(Request $request)
    {
        $filename = 'FinArchive.xlsx';
        return Excel::download(new StatisticsFinArchiveExport(@$request->date), $filename);
    }
    public function statFinArchive2(Request $request)
    {
        $start=null;
        $end=null;
        if(isset($request->StartDate) && isset($request->EndDate)){
            $start = Verta::parse($request->StartDate)->toCarbon()->format('Y-m-d');
            $end = Verta::parse($request->EndDate)->toCarbon()->format('Y-m-d');
            $s = Carbon::parse($start);
            $e = Carbon::parse($end);
            if (!$e->gt($s))
            {
                $start=null;
                $end=null;
            }
        }
        $filename = 'FinArchive2.xlsx';
        return Excel::download(new StatisticsFinArchive2Export($start,$end), $filename);
    }
    public function DriversWastesWeight(Request $request)
    {
        $filename = 'DriversWastesWeight.xlsx';
        return Excel::download(new DriversWastesWeightExport(@$request->date), $filename);
    }
    public function statCashout(Request $request)
    {
        $filename = 'statCashout.xlsx';
        return Excel::download(new StatisticsCashoutExport(@$request->status), $filename);
    }
}
