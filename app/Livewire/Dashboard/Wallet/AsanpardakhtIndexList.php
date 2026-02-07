<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\AsanPardakht;
use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AsanpardakhtIndexList extends Component
{
    use WithPagination;
    #[Url]
    public $status;
    #[Url]
    public $search;
    #[Url]
    public $dateFrom;
    #[Url]
    public $dateTo;
    public $row = 7;
    public function render()
    {
        return view('livewire.dashboard.wallet.asanpardakht-index-list');
    }

    #[Computed]
    public function transactions()
    {
        $query = AsanPardakht::query();

        if($this->status == 'deposit'){
            $query->where('method', 'واریز');
        }
        elseif($this->status == 'withdraw'){
            $query->where('method', 'برداشت');
        }
        elseif($this->status == 'sharj'){
            $query->where('type', 'asanpardakht_sharj');
        }

        if($this->search){
            $query->whereHas('user', function (Builder $query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('guild_title', 'like', '%'.$this->search.'%')
                        ->orWhere('lastname', 'like', '%'.$this->search.'%')
                        ->orWhere('mobile', 'like', '%'.$this->search.'%')
                        ->orWhereRaw("CONCAT(name, ' ', lastname) LIKE '%{$this->search}%'");
                });
            })->orWhere('rrn', 'like', '%'.$this->search.'%');
        }
        if ($this->dateFrom && $this->dateTo) {
            $dateFrom = toGregorian($this->dateFrom,'/','-',false);
            $dateTo = toGregorian($this->dateTo,'/','-',false);
            $query->whereBetween('created_at', [$dateFrom.' 00:00:00', $dateTo.' 23:59:59']);
        }
        elseif ($this->dateFrom && !$this->dateTo) {
            $dateFrom = toGregorian($this->dateFrom,'/','-',false);
            $query->where('created_at', '>',$dateFrom.' 00:00:00');
        }
        elseif (!$this->dateFrom && $this->dateTo) {
            $dateTo = toGregorian($this->dateTo,'/','-',false);
            $query->where('created_at', '<=',$dateTo.' 23:59:59');
        }
        $row = isset($_COOKIE['table-row']) ? $_COOKIE['table-row'] : $this->row;
        return $query->latest()->paginate($row);
    }

    #[On('status')]
    public function status($status)
    {
        $this->status = $status;
        $this->resetPage();
    }

    #[On('search')]
    public function search($search)
    {
        $this->search = $search;
        $this->resetPage();
    }

    #[On('dateFrom')]
    public function dateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
        $this->resetPage();
    }

    #[On('dateTo')]
    public function dateTo($dateTo)
    {
        $this->dateTo = $dateTo;
        $this->resetPage();
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
