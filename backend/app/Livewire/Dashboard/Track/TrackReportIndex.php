<?php

namespace App\Livewire\Dashboard\Track;

use App\Models\Car;
use Livewire\Component;

class TrackReportIndex extends Component
{
    public $breadCrumb = [
        ['گزارش ارتباط با سرور','d.track.report'],
    ];
    public function render()
    {
        $this->authorize('track_report_index', TrackReportIndex::class);
        $cars = Car::where('is_active', true)->with('user.polygonDrivers.polygon')->get();
        return view('livewire.dashboard.track.track-report-index', compact('cars'));
    }
}
