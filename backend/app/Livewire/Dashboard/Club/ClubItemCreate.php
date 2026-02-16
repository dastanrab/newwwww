<?php

namespace App\Livewire\Dashboard\Club;

use App\Models\Club;
use App\Models\ClubCategory;
use App\Models\User;
use Exception;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class ClubItemCreate extends Component
{
    use WithFileUploads;
    public $breadCrumb = [['باشگاه مشتریان','d.club.items'],['افزودن','d.club.create']];
    #[Title('باشگاه مشتریان > افزودن')]

    public $title;
    public $subTitle;
    public $content;
    public $image;
    public $brandIcon;
    public $score;
    public $status;
    public $category;
    public $userId;
    public $categories;
    public $marketer;
    public $discount_type;
    public $discount_value;
    public $site;

    public function boot()
    {
        $this->categories = ClubCategory::all();
        $this->marketer = User::whereHas('roles',fn ($q) => $q->where('name','marketer'))->get();
    }

    public function render()
    {
        $this->authorize('club_create', Club::class);
        return view('livewire.dashboard.club.club-item-create');
    }

    public function store()
    {
        $maxImage = 1024*2;// 2MB Max
        $maxBrandIcon = 1024;// 1MB Max
        $this->validate([
            'title'     => 'required|min:2',
            'subTitle'  => 'required|min:2',
            'image'     => 'image|max:'.$maxImage,
            'brandIcon' => 'image|max:'.$maxBrandIcon,
            'score'     => 'required|int',
            'status'     => 'in:active,inActive',
            'discount_type'     => 'required|in:1,2',
            'site'     => 'required|in:1,2',
            'discount_value'     => 'required|numeric',
        ],[
            'title.required' => 'عنوان اجباری می باشد',
            'subTitle.required' => 'زیرعنوان اجباری می باشد',
            'title.min' => 'حداقل کاراکتر عنوان :min می باشد',
            'image.max' => 'حداکثر سایز آپلود تصویر :max می باشد',
            'brandIcon.max' => 'حداکثر سایز آپلود تصویر برند :max می باشد'
        ]);

        $path = now()->format('Y/m/d').'/'.strRandom('10').'-'.$this->image->getClientOriginalName();
        $image = $this->image->storeAs('',$path,'bazist');

        $path = now()->format('Y/m/d').'/'.strRandom('10').'-'.$this->brandIcon->getClientOriginalName();
        $brandIcon = $this->brandIcon->storeAs('',$path,'bazist');

        try {
            $club = Club::create([
                'user_id' => $this->userId,
                'title' => $this->title,
                'sub_title' => $this->subTitle,
                'content' => $this->content,
                'image' => 'uploads/'.$image,
                'brand_icon' => 'uploads/'.$brandIcon,
                'score' => $this->score,
                'status' => $this->status,
                'discount_type' => (int)$this->discount_type,
                'discount_value'=>(int)$this->discount_value,
                'has_site' => (int)$this->site,
            ]);
            $club->categories()->sync([$this->category]);
            return $this->redirect(route('d.club.edit',$club->id),true);
        }
        catch (Exception $e){
            return sendToast(0,$e->getMessage());
        }

    }
}
