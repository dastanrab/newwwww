<?php

namespace App\Livewire\Dashboard\Club;

use App\Models\Club;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class ClubItemIndexList extends Component
{

    #[Url]
    public $category;
    #[Url]
    public $search;

    public function render()
    {
        return view('livewire.dashboard.club.club-item-index-list');
    }

    #[Computed]
    public function club()
    {
        $club = Club::query();
        if($this->category){
            $categoryId = $this->category;
            $club = $club->whereHas('categories', function ($query) use($categoryId) {
                $query->where('category_id', $categoryId);
            });
        }
        if($this->search){
            $search = $this->search;
            $club = $club->where(function ($query) use($search) {
                $query
                    ->where('title', 'LIKE', "%$search%")
                    ->orWhere('sub_title', 'LIKE', "%$search%")
                    ->orWhere('score', $search);
            });
        }
        $club = $club->orderBy('created_at', 'desc')->paginate(20);
        return $club;
    }

    #[On('filterCategory')]
    public function filterCategory($value)
    {
        $this->category = $value;
    }

    #[On('search')]
    public function search($value)
    {
        $this->search = $value;
    }
    public function update_status($id,$value)
    {
        $club=Club::find($id);
        if ($club){
            $club->status=$value == '1' ? 'active' : 'deactivate';
            $club->save();
             sendToast(1,'وضعیت  با موفقیت تغییر کرد');
             return true;
        }
        sendToast(0,'موردی برای تغییر یافت نشد ');
        return false;
    }

}
