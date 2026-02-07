<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\DriversSalaryDetails;
use App\Models\Submit;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StatOtherTotalIndexList extends Component
{
    use WithPagination;
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    #[Url]
    public $type = 0;
    #[Url]
    public $op = 0;
    #[Url]
    public $field = [];
    private $types;
    private $drivers_fields=['distance','total_attendance','weight'];
    private $wallet_fields=[3=>'wallet_deposite'];
    public $rows;
    public $operators_symbols=['SUM','SUM','AVG','COUNT'];
    public $column=[];
    public array $options = ['مسافت','حاضر به کار','وزن','مجموع واریز','مجموع برداشت','بالانس حساب'];

    public function __construct()
    {
        $this->column=$this->field;
    }

    public function render()
    {
        return view('livewire.dashboard.stats.stat-other-total-index-list');
    }

  public function getTypeRowName()
  {
      if (!isset($this->type) or  $this->type == 1)
      {
          return 'نام و نام خانوادگی راننده';
      }
      else{
          return 'شناسه درخواست';
      }
  }
    #[Computed]
    public function data()
    {
      return  $this->fetchTypeData();
    }

    private function fetchTypeData()
    {
            if ($this->type == 1 or $this->type == 0)
            {
               return $this->Drivers();
            }
            else{
                return $this->Submits();
            }
    }
    private function Drivers()
    {
        $query = User::query()->select(['id','name','lastname'])->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        });
        $query=$query->orderBy('created_at', 'desc')->paginate(15);
        $this->rows=$this->prepareRows($query);
        return $query;
    }
    private function Submits()
    {
        $query= Submit::query()->select(['id'])->where('status',3)->paginate(15);
        $this->rows=$this->prepareRows($query);
        return $query;
    }
    private function prepareRows($data)
    {
        $this->column=$this->field;
        $rows=[];
        $ids=$data->getCollection()->pluck('id')->toArray();
        $fields=$this->FieldsData($ids);
        $data->getCollection()->transform(function ($d) use(&$rows,$fields) {
            if (isset($fields[$d->id]))
            {
                $rows[$d->id][]=$this->TypeColumn($d);
                foreach ($fields[$d->id] as $field)
                {
                    $rows[$d->id][]=$field;
                }
            }
            else{
                $rows[$d->id] = array_fill(0, count($this->column)+1, 0);
                $rows[$d->id][0]=$this->TypeColumn($d);
            }
            return $d;
        });
        return $rows;
    }
    private function TypeColumn($data)
    {
       if ($this->type == 1 or $this->type == 0)
       {
          return @$data->name.' '.@$data->lastname;
       }else{
           return @$data->id;
       }
    }

    #[On('type')]
    public function type($type)
    {
        $this->type = $type;
    }

    #[On('op')]
    public function op($op)
    {
        $this->op = $op;
    }

    #[On('field')]
    public function field($field)
    {
        $this->column = $field;
        $this->field = $field;
    }
    #[On('dateFrom')]
    public function dateFrom($date)
    {
        $this->dateFrom = $date;
    }
    #[On('dateTo')]
    public function dateTo($date)
    {
        $this->dateTo = $date;
    }

    private function FieldsData($ids)
    {
        if ($this->type == 0 or $this->type == 1)
        {
            $salary=[];
            $wallet=[];
            $salary_fields=array_intersect(array_values($this->field),array_keys($this->drivers_fields));
            if (count($salary_fields)>0)
            {
                $salary =  $this->DriversSalaryinfo($ids);
            }
            $wallet_fields=array_intersect(array_values($this->field),array_keys($this->wallet_fields));
            if (count($wallet_fields))
            {
                $this->column = array_merge($this->column,['4','5']);
                $wallet = $this->WalletDeopsite($ids);
            }
            if (count($salary) > 0)
            {
                foreach ($salary as $key =>$item)
                {
                    if (isset($wallet[$key]))
                    {
                        foreach ($wallet[$key] as $value)
                        {
                            $salary[$key][]=$value;
                        }
                    }else{
                        for ($i=0;$i<count($wallet_fields);$i++)
                        {
                            $salary[$key][]=0;
                        }
                    }
                }
                return $salary;            }
            else{
                return $wallet;
            }
        }
//        else{
//          return  $this->SubmitsInfo($ids);
//        }

    }
    private function SubmitsInfo($ids)
    {

    }
    private function DriversSalaryinfo($ids)
    {
        $op= $this->getActiveOp();
        if (isset($this->field))
        {
            $select=' user_id';
            foreach ($this->field as $item)
            {
                if (isset($this->drivers_fields[(int)$item]))
                {
                    $select.=' , '.$op.'('.$this->drivers_fields[(int)$item].')';
                }
            }
            $result = DriversSalaryDetails::query()->select([DB::raw($select)]);
            $result=$result->whereIn('user_id',$ids);
            if (isset($this->dateTo) and isset($this->dateFrom))
            {
                $dateFrom = Verta::parse($this->dateFrom)->toCarbon()->format('Y-m-d 00:00:00');
                $dateTo = Verta::parse($this->dateTo)->toCarbon()->format('Y-m-d 23:59:59');
                $result=$result->whereBetween('created_at',[$dateFrom,$dateTo]);
            }
            else{
                $result=$result->whereDate('created_at','>',Carbon::now()->subDays(30));
            }

            $result=$result->groupBy('user_id')->get();
            $result=$result->keyBy('user_id')->each(function ($data) {
                unset($data['user_id']);
            })->toArray();
            return $result;
        }
        else{
            return [];
        }
    }

    private function WalletDeopsite($ids)
    {
        $wallet_ids=Wallet::query()->select('id')->whereIn('user_id',$ids)->get()->pluck('id')->toArray();
        $result= DB::table('wallet_details')
            ->selectRaw(" user_id ,
        SUM(IF(method = 'واریز', amount, 0)) AS vaariz_amount,
        SUM(IF(method = 'برداشت', -CAST(amount AS SIGNED), 0)) AS bardaasht_amount,
        SUM(IF(method = 'واریز', amount, -CAST(amount AS SIGNED))) AS balance_amount
    ")
            ->whereIn('wallet_id', $wallet_ids)
            ->groupBy('user_id')
            ->get();
        return $result->keyBy('user_id')->each(function ($data) {
            unset($data->user_id);
        })->toArray();
    }

    private function getActiveOp()
    {
        if ($this->op == 0 or $this->op == 1)
        {
            return $this->operators_symbols[0];

        }else{
            return $this->operators_symbols[$this->op];
        }
    }

}
