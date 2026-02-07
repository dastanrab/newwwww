<?php

namespace App\Livewire\Dashboard\Club;

use App\Models\Club;
use App\Models\ClubCategory;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class ClubCategoryCreate extends Component
{
    use WithFileUploads;
    public $breadCrumb = [['باشگاه مشتریان','d.club.items'],['دسته بندی','d.club.categories'],['افزودن','d.club.category.create']];
    #[Title('باشگاه مشتریان > دسته بندی > افزودن')]

    public $title;
    public $icon;

    public function render()
    {
        $this->authorize('club_category_create', Club::class);
        return view('livewire.dashboard.club.club-category-create');
    }

    public function store()
    {
        $max = 1024*2;// 2MB Max
        $this->validate([
            'title' => 'required|unique:club_categories,title|min:2',
            'icon' => 'image|max:'.$max,
        ],[
            'title.required' => 'عنوان اجباری می باشد',
            'title.min' => 'حداقل کاراکتر عنوان :min می باشد',
            'icon.max' => 'حداکثر سایز آپلود :max می باشد'
        ]);

        $path = now()->format('Y/m/d').'/'.strRandom('10').'-'.$this->icon->getClientOriginalName();
        $upload = $this->icon->storeAs('',$path,'bazist');

        $create = ClubCategory::create([
            'title' => $this->title,
            'icon' => 'uploads/'.$upload,
        ]);
        if($create){
            $this->reset('title','icon');
            sendToast(1,'با موفقیت ایجاد شد');
        }
        else{
            sendToast(0,'ثبت با اشکال روبرو شد');
        }
    }
}
