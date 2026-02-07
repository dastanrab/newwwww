<?php

namespace App\Livewire\Dashboard\Club;

use App\Models\Offer;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ClubOfferIndexList extends Component
{
    public $listType=0;
    public function render()
    {
        return view('livewire.dashboard.club.club-offer-index-list');
    }

    #[Computed]
    public function offers()
    {
        $offers = Offer::with(['user','club']);
        if($this->listType == 1)
        {
           $offers=$offers->where('used',1);
        }else{
            $offers=$offers->where('used',0);
        }
        $offers=$offers->orderBy('id','DESC')->paginate(20);
        return $offers;
    }
    #[On('delete')]
    public function delete($offerId)
    {
        $offer = Offer::findOrFail($offerId);
        $offer->delete();
        return sendToast(1,'حذف شد');
    }

}
