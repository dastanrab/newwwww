<?php

namespace App\Livewire\Dashboard\Club;

use App\Models\Club;
use App\Models\ClubCategory;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class ClubCategoryEdit extends Component
{

    use WithFileUploads;
    public $breadCrumb = [['باشگاه مشتریان','d.club.items'],['دسته بندی','d.club.categories'],['ویرایش','d.club.category.edit']];
    #[Title('باشگاه مشتریان > دسته بندی > ویرایش')]

    public ClubCategory $clubCategory;
    public $title;
    public $icon;

    public function boot()
    {
        $this->title = $this->clubCategory->title;
    }

    public function render()
    {
        $this->authorize('club_category_edit', Club::class);
        return view('livewire.dashboard.club.club-category-edit');
    }

    public function update()
    {
        $max = 1024*2;// 2MB Max
        $this->validate([
            'title' => 'required|min:2|unique:club_categories,title,'.$this->clubCategory->id,
            'icon' => $this->icon ? 'image|max:'.$max : 'nullable',
        ],[
            'title.required' => 'عنوان اجباری می باشد',
            'title.min' => 'حداقل کاراکتر عنوان :min می باشد',
            'icon.max' => 'حداکثر سایز آپلود :max می باشد'
        ]);

        $this->clubCategory->title = $this->title;
        if($this->icon){
            $path = now()->format('Y/m/d').'/'.strRandom('10').'-'.$this->icon->getClientOriginalName();
            $upload = $this->icon->storeAs('',$path,'bazist');
            $this->clubCategory->icon = 'uploads/'.$upload;
        }
        $update = $this->clubCategory->save();
        if($update){
            sendToast(1,'با موفقیت ویرایش شد');
            return redirect(request()->header('Referer'));
        }
        else{
            sendToast(0,'ویرایش با اشکال روبرو شد');
        }
    }
}
