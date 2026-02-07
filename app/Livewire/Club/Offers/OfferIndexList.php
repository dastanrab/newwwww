<?php

namespace App\Livewire\Club\Offers;

use App\Models\Offer;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class OfferIndexList extends Component
{
    use WithPagination;
    #[Url]
    public $search;

    public function render()
    {
        return view('livewire.club.offers.offer-index-list');
    }

    #[Computed]
    public function offers()
    {
        $user = auth()->user();
        $offers = Offer::whereIn('club_id', function ($query) use ($user) {
            $query->select('id')
                ->from('clubs')
                ->where('user_id', $user->id);
        });
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
    #[On('used')]
    public function used($offerId)
    {
        $offer = Offer::findOrFail($offerId);
        $user = auth()->user();
        if($offer->club->user_id != $user->id)
            return sendToast(0,'درخواست وجود ندارد');
        $offer->used = 1;
        $offer->save();
        return sendToast(1,'ثبت شد');
    }
}
