<?php

namespace App\Livewire\Dashboard\Submits;

use App\Models\Submit;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class SubmitTelIndex extends Component
{

    public $breadCrumb = [['درخواست تلفنی','d.submits.tel']];
    #[Title('درخواست تلفنی')]

    public $step = 1;
    #[Url]
    public $userId;
    public $search;

    public function mount()
    {
        if ($this->userId){
            $user = User::find($this->userId);
            if($user){
                $this->step = 2;
            }
            else{

                abort(404);
            }
        }
    }

    public function render()
    {
        $this->authorize('submit_tel_index',Submit::class);
        return view('livewire.dashboard.submits.submit-tel-index');
    }

    #[On('step')]
    public function step($data)
    {
        $this->step = $data['step'];
        $this->userId = $data['userId'];
    }
}
