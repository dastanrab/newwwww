<?php

namespace App\Livewire\Dashboard\Activity;

use App\Events\ActivityEvent;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Activity;
use Livewire\WithPagination;

class ActivityIndex extends Component
{
    public  $breadCrumb = [['فعالیت ها','d.activity']];
    #[Title('فعالیت ها')]
    public function render()
    {
        $this->authorize('activity_index',Activity::class);
        return view('livewire.dashboard.activity.activity-index');
    }
}
