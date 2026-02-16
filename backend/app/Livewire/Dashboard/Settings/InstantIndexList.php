<?php

namespace App\Livewire\Dashboard\Settings;

use App\Models\Polygon;
use App\Models\Role;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class InstantIndexList extends Component
{
    use WithPagination;
    #[Url(as: 'q', history: true)]
    public $search;
    public $row;
    public function polygons()
    {
        $query=Polygon::query()->select(['id','region','has_instant','has_legal_collect','has_illegal_collect']);
        if(!empty($this->search)){
            $this->dispatch('filter-search',$this->search);
            $query->WhereRaw("region like '%{$this->search}%'");
        }
        return $query->orderBy('id')->paginate($this->row);
    }

    public function update_instant($polygon_id,$value)
    {
        $polygon=Polygon::find($polygon_id);
        if ($polygon){
            $polygon->has_instant=$value == '1' ? 1 : 0;
            $polygon->save();
            return sendToast(1,'وضعیت  با موفقیت تغییر کرد');
        }
        return sendToast(0,'موردی برای تغییر یافت نشد ');

    }
    public function instant_all()
    {
        $polygon=Polygon::query()->update(['has_instant'=>1]);
        if ($polygon){
            return sendToast(1,'وضعیت ها با موفقیت تغییر کرد');
        }
        return sendToast(0,'موردی برای تغییر یافت نشد ');

    }
    public function deinstant_all()
    {
        $polygon=Polygon::query()->update(['has_instant'=>0]);
        if ($polygon){
            return sendToast(1,'وضعیت ها با موفقیت تغییر کرد');
        }
        return sendToast(0,'موردی برای تغییر یافت نشد ');

    }
    public function update_legal($polygon_id,$value)
    {
        $polygon=Polygon::find($polygon_id);
        if ($polygon){
            $polygon->has_legal_collect=$value == '1' ? 1 : 0;
            $polygon->save();
            return sendToast(1,'وضعیت  با موفقیت تغییر کرد');
        }
        return sendToast(0,'موردی برای تغییر یافت نشد ');
    }
    public function update_illegal($polygon_id,$value)
    {
        $polygon=Polygon::find($polygon_id);
        if ($polygon){
            $polygon->has_illegal_collect=$value == '1' ? 1 : 0;
            $polygon->save();
            return sendToast(1,'وضعیت  با موفقیت تغییر کرد');
        }
        return sendToast(0,'موردی برای تغییر یافت نشد ');
    }
    #[On('search')]
    public function search($search)
    {
        $this->resetPage();
        $this->search = $search;
    }
    public function render()
    {
        return view('livewire.dashboard.settings.instant-index-list');
    }
    #[On('row')]
    public function row($row)
    {
        $this->resetPage();
        if(isset($_COOKIE['table-row'])) unset($_COOKIE['table-row']);
        setcookie('table-row',$row,time() + ((86400 * 365))*10, "/"); // 86400 = 1 day
        $this->row = $row;
    }
}
