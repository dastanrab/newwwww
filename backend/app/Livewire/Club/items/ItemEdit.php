<?php

namespace App\Livewire\Club\items;

use App\Models\Club;
use App\Models\Offer;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ItemEdit extends Component
{
    use WithPagination;
    public $breadCrumb = [
        ['تخفیف ها','cl.items'],
        ['جزئیات','cl.item.edit'],
    ];
    #[Title('تخفیف ها > جزئیات')]

    public Club $club;
    #[Url]
    public $search;
    public function render()
    {
        $this->authorize('view',$this->club);
        return view('livewire.club.items.item-edit');
    }

    #[Computed]
    public function offers()
    {
        $user = auth()->user();
        $offers = $this->club->offers();
        if($this->search){
            $offers = $offers->where('code',$this->search)->orWhereHas('user',function($q){
                $q->where('mobile','LIKE',"%{$this->search}%")
                    ->orWhereRaw("CONCAT(name, ' ', lastname) LIKE '%{$this->search}%'");
            });
        }
        return $offers->orderByDesc('id')->paginate(10);
    }

    #[On('search')]
    public function search($search)
    {
        $this->search = $search;
        $this->resetPage();
    }

}
