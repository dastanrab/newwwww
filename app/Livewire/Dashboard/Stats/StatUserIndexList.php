<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\City;
use App\Models\User;
use App\Models\UserComment;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StatUserIndexList extends Component
{
    use WithPagination;
    #[Url]
    public $search;
    #[Url]
    public $isLegal;
    public $row = 7;
    public $comment;
    private mixed $city_id;

    public function boot()
    {
        $this->city_id = session('city',1) ;
        if ($this->city_id == 0){
            $this->city_id=City::all()->pluck('id')->toArray();
        }
        else{
            $this->city_id=[$this->city_id];
        }
    }
    #[On('city')]
    public function city($city)
    {
        $this->city_id = session('city',1) ;
        if ($this->city_id == 0){
            $this->city_id=City::all()->pluck('id')->toArray();
        }
        else{
            $this->city_id=[$this->city_id];
        }
    }
    public function render()
    {
        return view('livewire.dashboard.stats.stat-user-index-list');
    }

    #[Computed]
    public function users()
    {
        $query = User::with('roles')->whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'supervisor', 'operator', 'accountants', 'marketer', 'user','driver']);
        });
        if(!empty($this->search)){
            $query = $query->whereRaw("CONCAT(name, ' ', lastname) LIKE '%{$this->search}%'")
                ->orWhere('guild_title', 'like', '%'.$this->search.'%')
                ->orWhere('mobile', 'like', '%'.$this->search.'%')
                ->orWhere('national_code', 'like', '%'.$this->search.'%');

        }
        if(in_array($this->isLegal,['0','1']) ){
            $isLegal = $this->isLegal == '1' ? 1 : 0;
            $query->where('legal',$isLegal);
        }
        $query=$query->where('city_id',$this->city_id);
        $row = isset($_COOKIE['table-row']) ? $_COOKIE['table-row'] : $this->row;
        return $query->orderBy('created_at', 'desc')->paginate($row);
    }

    public function storeComment(User $user)
    {
        $this->validate([
            'comment' => 'required|string',
        ]);

        $message = new UserComment;
        $message->user_id = $user->id;
        $message->operator_id = auth()->id();
        $message->text = $this->comment;
        $message->save();
        $this->reset('comment');
    }

    #[On('search')]
    public function search($search)
    {
        $this->resetPage();
        $this->search = $search;
    }

    #[On('isLegal')]
    public function isLegal($isLegal)
    {
        $this->resetPage();
        $this->isLegal = $isLegal;
    }

    #[On('row')]
    public function row($row)
    {
        $this->resetPage();
        if(isset($_COOKIE['table-row'])) unset($_COOKIE['table-row']);
        setcookie('table-row',$row,time() + ((86400 * 365))*10, "/"); // 86400 = 1 day
        $this->row = $row;
    }
}
