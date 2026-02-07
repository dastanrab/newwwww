<?php
namespace App\Livewire\Club\items;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
class ItemIndexList extends Component
{
    use WithPagination;
    public function render()
    {
        return view('livewire.club.items.item-index-list');
    }

    #[Computed]
    public function club()
    {
        $user = auth()->user();
        $clubs = $user->clubs()->orderByDesc('id')->paginate(10);
        return $clubs;
    }

}
