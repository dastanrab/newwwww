<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatOtherTotalIndexFilter extends Component
{
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    #[Url]
    public $type = 0;
    #[Url]
    public $field = [];
    #[Url]
    public $op;
    public array $types = ['انتخاب کنید','رانندگان'];
    public array $options = ['مسافت','حاضر به کار','وزن','تراز حساب'];
    public $operators_fa = ['انتخاب کنید','مجموع','میانگین','تعداد'];
    public function render()
    {
        $this->setOptions();
        return view('livewire.dashboard.stats.stat-other-total-index-filter');
    }
    public function setOptions()
    {
        if ($this->type == 2)
        {
            $this->options=['وزن'];
        }
    }

    public function updated($prop)
    {

        if($prop == 'type'){
            $this->setOptions();
            $this->field=[];
            $this->dispatch('type',$this->type);
            $this->dispatch('options',json_encode($this->options));
        }
        elseif($prop == 'field'){
            $this->dispatch('field',$this->field);
        }
        elseif($prop == 'op'){
            $this->dispatch('op',$this->op);
        }elseif ($prop == 'dateFrom'){
            $this->dispatch('dateFrom',$this->dateFrom);
        }elseif ($prop == 'dateTo'){
            $this->dispatch('dateTo',$this->dateTo);
        }
    }

}
