<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessTopicFirebase;
use Illuminate\Http\Request;

class FcmController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $firebase = $user->firebases()->where('platform','dashboard')->first();
        if($firebase){
            $firebase->update(['token' => $request->token]);
        }
        else{
            $user->firebases()->create(['platform' => 'dashboard', 'token' => $request->token]);
        }
        $topics = ['all','allDashboard'];
        ProcessTopicFirebase::dispatch($topics,$request->token);
    }
}
