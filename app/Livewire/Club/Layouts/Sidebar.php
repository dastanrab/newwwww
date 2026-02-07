<?php

namespace App\Livewire\Club\Layouts;

use App\Models\Contact;
use App\Models\Permission;
use Livewire\Component;

class Sidebar extends Component
{
    public function render()
    {
        $menu = [
            [
                'title' => 'داشبورد',
                'icon'  => 'bx bxs-dashboard',
                'route' => route('cl.dashboard'),
                'name' => 'dashboard',
            ],
            [
                'title' => 'تخفیف ها',
                'icon'  => 'bx bxs-offer',
                'route' => route('cl.offers'),
                'name' => 'offers',
            ],
            [
                'title' => 'آیتم ها',
                'icon'  => 'bx bx-box',
                'route' => route('cl.items'),
                'name' => 'items',
            ]
        ];
        $permissions = auth()->user()->getPermissions();
        return view('livewire.club.layouts.sidebar',compact('menu','permissions'));
    }
}
