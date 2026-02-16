<?php

namespace App\Livewire\Dashboard\Settings;

use Livewire\Attributes\Title;
use Livewire\Component;

class RolesIndex extends Component
{

    public $breadCrumb = [['سطح دسترسی','d.settings.roles']];
    #[Title('سطح دسترسی')]
    public function render()
    {
        $this->authorize('setting_role_index',RolesIndex::class);
        return view('livewire.dashboard.settings.roles-index');
    }
}
