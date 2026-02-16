<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function fava()
    {
        return response()->json(['message' => 'Dashboard Fava']);
    }

    public function finance()
    {
        return response()->json(['message' => 'Dashboard Finance']);
    }
}
