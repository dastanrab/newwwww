<?php

namespace App\Livewire\Dashboard\Club;

use App\Models\Club;
use App\Models\Offer;
use Exception;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class ClubOffersCreate extends Component
{
    use WithFileUploads;
    public $breadCrumb = [['باشگاه مشتریان','d.club.offers-open'],['افزودن','d.club.offers.create']];
    #[Title(' باشگاه مشتریان > افزودن')]

    public $title;
    public $club;
    public $count;
    public  $items;

    public function boot()
    {
        $club =  Club::query()->select(['id','title','score'])->where('status','active')->orderBy('created_at', 'desc')->get();
        $this->items = $club;
    }


    public function render()
    {
        $this->authorize('club_create', Club::class);
        return view('livewire.dashboard.club.club-offers-create');
    }

    public function store()
    {
        $this->validate([
            'title'     => 'required|min:2',
            'count'  => 'required',
        ],[
            'title.required' => 'عنوان اجباری می باشد',
            'count.required' => 'تعداد اجباری می باشد',
            'title.min' => 'حداقل کاراکتر عنوان :min می باشد'
        ]);
        $club=Club::query()->where('id',(int)$this->club)->first();
        try {
            for ($i = 0; $i < (int)$this->count; $i++) {
                    $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                while (Offer::query()->where('code',$code)->exists()) {

                        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                }
                Offer::create([
                    'club_id' => $club->id,
                    'score' => $club->score,
                    'title' => $this->title,
                    'code' => $code,
                    'used'=>0
                ]);
            }

            return $this->redirect(route('d.club.offers'),true);
        }
        catch (Exception $e){
            return sendToast(0,$e->getMessage());
        }

    }
}
