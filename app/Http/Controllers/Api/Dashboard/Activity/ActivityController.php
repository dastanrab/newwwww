<?php

namespace App\Http\Controllers\Api\Dashboard\Activity;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Support\Facades\Request;

class ActivityController extends Controller
{
    public function index(Request$request){
        $query = Activity::with('user');

        // فیلتر تاریخ شروع
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        // فیلتر تاریخ پایان
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $activities = $query->latest()->paginate(50);

        return success_response($activities);
    }
}
