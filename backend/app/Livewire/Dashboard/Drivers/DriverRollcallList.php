<?php

namespace App\Livewire\Dashboard\Drivers;

use App\Models\Car;
use App\Models\Rollcall;
use App\Models\User;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DriverRollcallList extends Component
{


    use WithPagination;

    public User $driver;
    public int $hour;
    public int $min;
    public int $endHour;
    public int $endMin;
    public int $startHour;
    public int $startMin;

    public $description;

    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;

    public function mount()
    {
        $this->hour = now()->format('H');
        $this->min = now()->format('i');
        $this->endHour = now()->format('H');
        $this->endMin = now()->format('i');
    }
    #[Computed]
    public function rollcalls()
    {
        $query = Rollcall::query();
        $query->where('user_id',$this->driver->id);
        if($this->dateFrom && $this->dateTo){
            $dateFrom = Verta::parse($this->dateFrom)->toCarbon()->format('Y-m-d');
            $dateTo = Verta::parse($this->dateTo)->toCarbon()->format('Y-m-d');
            $query->whereBetween('start_at',[$dateFrom.' 00:00:00',$dateTo.' 23:59:59']);
        }
        elseif ($this->dateFrom){
            $dateFrom = Verta::parse($this->dateFrom)->toCarbon()->format('Y-m-d');
            $query->where('start_at','>=',$dateFrom.' 00:00:00');
        }
        elseif ($this->dateTo){
            $dateTo = Verta::parse($this->dateTo)->toCarbon()->format('Y-m-d');
            $query->where('start_at','<=',$dateTo.' 23:59:59');
        }
        $query = $query->orderBy('start_at', 'desc')->paginate(10);
        return $query;
    }

    public function render()
    {
        return view('livewire.dashboard.drivers.driver-rollcall-list');
    }

    public function endRollcall(Rollcall $rollCall)
    {
        $this->validate([
            'hour' => 'required|numeric|between:1,23',
            'min' => 'required|numeric|between:0,59',
            'description' => 'required|min:5',
        ],[
            'hour' => 'ساعت را وارد نمایید',
            'min' => 'دقیقه را وارد نمایید',
        ]);
        $rollCall->end([
            'hour' => $this->hour,
            'min' => $this->min,
            'description' => $this->description,
        ]);
        $this->dispatch('remove-modal');
        sendToast(1,'حضور پایان یافت');
    }

    public function editRollcall(Rollcall $rollcall)
    {
        $validator = Validator::make($this->all(), [
            'endHour' => 'required|numeric|between:1,23',
            'endMin' => 'required|numeric|between:0,59',
            'description' => 'required|min:5',
        ], [
            'endHour' => 'ساعت پایان را وارد نمایید',
            'endMin' => 'دقیقه پایان را وارد نمایید',
        ]);
        if ($validator->fails()):
            $this->dispatch('remove-modal');
            sendToast(0,$validator->errors()->first());
        else:
            $rollcall->edit([
                'endHour' => $this->endHour,
                'endMin' => $this->endMin,
                'description' => '(ویرایش) '.$this->description,
            ]);
            $this->dispatch('remove-modal');
            sendToast(1,'حضور ویرایش شد');
        endif;
    }

    public function getRollCall(Rollcall $rollcall)
    {
        $this->startHour = Carbon::parse($rollcall->start_at)->format('H');
        $this->startMin = Carbon::parse($rollcall->start_at)->format('i');
        $this->endHour = $rollcall->end_at != null ? Carbon::parse($rollcall->end_at)->format('H') : now()->format('H');
        $this->endMin = $rollcall->end_at != null ? Carbon::parse($rollcall->end_at)->format('i') : now()->format('i');
    }

    #[On('refresh-list')]
    public function refreshList()
    {
        $this->rollcalls();
    }

    #[On('dateFrom')]
    public function dateFrom($value)
    {
        $this->dateFrom = $value;
    }

    #[On('dateTo')]
    public function dateTo($value)
    {
        $this->dateTo = $value;
    }

}
