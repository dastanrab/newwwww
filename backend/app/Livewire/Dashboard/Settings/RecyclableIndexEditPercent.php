<?php

namespace App\Livewire\Dashboard\Settings;

use App\Models\Percentage;
use App\Models\Recyclable;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RecyclableIndexEditPercent extends Component
{
    public $recyclable;
    public $percents;
    public $isLegal;

    public function mount()
    {
        $this->percents = $this->recyclable->percentages()->where('is_legal', $this->isLegal)->pluck('percent','id')->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard.settings.recyclable-index-edit-percent');
    }

    #[Computed]
    public function percentages()
    {
        return Percentage::where('recyclable_id', $this->recyclable->id)->where('is_legal', $this->isLegal)->get();
    }

    public function update()
    {
        $main_price = Recyclable::find($this->recyclable->id)->price;
        foreach ($this->percents as $id => $percent){
            Percentage::find($id)->update(['percent' => $percent,'price' => ceil($main_price * $percent * 0.01)]);
        }
        sendToast(1,'با موفقیت ویرایش شد');
    }
}
