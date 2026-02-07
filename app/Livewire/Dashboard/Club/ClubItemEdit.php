<?php

namespace App\Livewire\Dashboard\Club;

use App\Models\Club;
use App\Models\ClubCategory;
use App\Models\User;
use Exception;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class ClubItemEdit extends Component
{
    use WithFileUploads;
    public $breadCrumb = [['باشگاه مشتریان','d.club.items'],['ویرایش','d.club.edit']];
    #[Title('باشگاه مشتریان > ویرایش')]
    public Club $club;
    public $title;
    public $subTitle;
    public $score;
    public $status;
    public $content;
    public $image;
    public $brandIcon;
    public $category;
    public $userId;
    public $categories;
    public $marketer;

    public function boot()
    {
        $this->title = $this->club->title;
        $this->subTitle = $this->club->sub_title;
        $this->score = $this->club->score;
        $this->content = $this->club->content;
        $this->status = $this->club->status;
        $this->category = $this->club->categories ? $this->club->categories->first()->id : null;
        $this->categories = ClubCategory::all();
        $this->userId = $this->club->user->id;
        $this->marketer = User::whereHas('roles',fn ($q) => $q->where('name','marketer'))->get();

    }

    public function render()
    {
        $this->authorize('club_edit', Club::class);
        return view('livewire.dashboard.club.club-item-edit');
    }

    public function update()
    {
        $maxImage = 1024*2;// 2MB Max
        $maxBrandIcon = 1024;// 1MB Max
        $this->validate([
            'title'     => 'required|min:2',
            'subTitle'  => 'required|min:2',
            'image'     =>  $this->image ? 'image|max:'.$maxImage : 'nullable',
            'brandIcon' => $this->brandIcon ? 'image|max:'.$maxBrandIcon : 'nullable',
            'score'     => 'required|int',
            'status'    => 'in:active,inActive',
        ],[
            'title.required' => 'عنوان اجباری می باشد',
            'subTitle.required' => 'زیرعنوان اجباری می باشد',
            'title.min' => 'حداقل کاراکتر عنوان :min می باشد',
            'image.max' => 'حداکثر سایز آپلود تصویر :max می باشد',
            'brandIcon.max' => 'حداکثر سایز آپلود تصویر برند :max می باشد'
        ]);

        try {
            $this->club->title = $this->title;
            $this->club->sub_title = $this->subTitle;
            $this->club->score = $this->score;
            $this->club->status = $this->status;
            $this->club->content = $this->content;
            $this->club->user_id = $this->userId;
            if($this->image){
                $path = now()->format('Y/m/d').'/'.strRandom('10').'-'.$this->image->getClientOriginalName();
                $image = $this->image->storeAs('',$path,'bazist');
                $this->club->image = 'uploads/'.$image;
            }
            if($this->brandIcon){
                $path = now()->format('Y/m/d').'/'.strRandom('10').'-'.$this->brandIcon->getClientOriginalName();
                $brandIcon = $this->brandIcon->storeAs('',$path,'bazist');
                $this->club->brand_icon = 'uploads/'.$brandIcon;
            }
            $update = $this->club->save();
            $this->club->categories()->sync([$this->category]);
            return sendToast(1,'با موفقیت ویرایش شد');
        }
        catch (Exception $e){
            return sendToast(0,$e->getMessage());
        }

    }
}
