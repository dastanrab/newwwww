<?php

namespace App\Livewire\Dashboard\Users;

use App\Models\City;
use App\Models\Role;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class UserIndexList extends Component
{
    use WithPagination;
    #[Url(as: 'q', history: true)]
    public $search;
    #[Url]
    public $isLegal;
    #[Url]
    public $role;
    #[Url]
    public $level;
    public $row = 7;
    /**
     * @var array|\Illuminate\Foundation\Application|\Illuminate\Session\SessionManager|mixed|mixed[]|null
     */
    private  $city_id;

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
    #[Computed]
    public function users()
    {
        if(!empty($this->role)){
            $this->dispatch('filter-role',$this->role);
            $roles = [$this->role];
        }
        else{
            $roles = Role::whereNotIn('name',['superadmin','driver'])->pluck('name');
        }
        $query = User::with('roles')->whereHas('roles', function ($query) use($roles) {
            $query->whereIn('name', $roles);
        });
        $query=$query->whereIn('city_id',$this->city_id);
        if(!empty($this->search)){
            $this->dispatch('filter-search',$this->search);
            $query->where(function ($query) {
                $query->orWhere('id',$this->search)
                    ->orWhere('mobile','LIKE',"%{$this->search}%")
                    ->orWhereRaw("CONCAT(name, ' ', lastname) LIKE '%{$this->search}%'");
            });
        }
        if($this->role == 'user' && in_array($this->isLegal,['0','1']) ){
            $this->dispatch('filter-is-legal',$this->isLegal);
            $isLegal = $this->isLegal == '1' ? 1 : 0;
            $query->where('legal',$isLegal);
        }
        if(!empty($this->level)){
            $query->where('level',$this->level);
        }
        $row = isset($_COOKIE['table-row']) ? $_COOKIE['table-row'] : $this->row;
        return $query->orderBy('created_at', 'desc')->paginate($row);
    }

    public function render()
    {
        return view('livewire.dashboard.users.user-index-list');
    }
    #[On('search')]
    public function search($search)
    {
        $this->resetPage();
        $this->search = $search;
    }

    #[On('role')]
    public function role($role)
    {
        $this->resetPage();
        $this->role = $role;
    }
    #[On('level')]
    public function level($level)
    {
        $this->resetPage();
        $this->level = $level;
    }

    #[On('isLegal')]
    public function isLegal($Legal)
    {
        $this->resetPage();
        $this->isLegal = $Legal;
    }

    #[On('row')]
    public function row($row)
    {
        $this->resetPage();
        if(isset($_COOKIE['table-row'])) unset($_COOKIE['table-row']);
        setcookie('table-row',$row,time() + ((86400 * 365))*10, "/"); // 86400 = 1 day
        $this->row = $row;
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
}
