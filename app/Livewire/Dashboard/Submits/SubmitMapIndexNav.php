<?php

namespace App\Livewire\Dashboard\Submits;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class SubmitMapIndexNav extends Component
{
    public $instantCount;
    public $submit9Count;
    public $submit11Count;
    public $submit13Count;
    public $submit15Count;
    public $submit17Count;
    public $activesCount;
    public $doneCount;
    #[Url]
    public $driver;
    public function render()
    {
        return view('livewire.dashboard.submits.submit-map-index-nav');
    }
    #[On('driver')]
    public function driver($driver)
    {
        $this->driver = $driver;
    }
}
