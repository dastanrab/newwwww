<?php

namespace App\Livewire\Dashboard\Messages;

use App\Models\Message;
use App\Models\Submit;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class SubmitSurveyIndex extends Component
{
    use WithPagination;
    public $breadCrumb = [
        ['نظرات درخواست ها','d.track.report'],
    ];
    public function render()
    {
        $this->authorize('notification_index',Message::class);
        return view('livewire.dashboard.messages.submit-surveys');
    }

    #[Computed]
    public function notifications()
    {
        return Submit::query()->select(['id','user_id','star','comment','created_at'])->with(['user','drivers.user'])->whereNotNull('comment')->where('star','<',3)->where('survey',1)->orderBy('id','DESC')->limit(100)->get();
        dd($submits);
    }
}
