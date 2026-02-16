<?php

namespace App\Livewire\Dashboard\Home;


use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class ChartsManagerFilter extends Component
{
    #[Url]
    public $date;
    #[Url]
    public $type;
    #[Url]
    public $dateFrom;

    public function render()
    {
        return view('livewire.dashboard.home.charts-manager-filter');
    }

    public function updated($property)
    {
       if ($property == 'date')
       {
           $this->dispatch('date',$this->date);
       }
       elseif($property == 'type'){
           $this->dispatch('type',$this->type);
       }else{
           $this->dispatch('dateFrom',$this->dateFrom);
       }
    }

    #[On('filter-date')]
    public function status($date)
    {
        $this->date = $date;
    }
    #[On('filter-type')]
    public function type($type)
    {
        $this->type = $type;
    }
    #[On('filter-dateFrom')]
    public function dateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

}
