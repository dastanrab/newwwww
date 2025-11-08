<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;

Route::prefix('dashboard')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/fava', [DashboardController::class, 'fava']);
    Route::get('/finance', [DashboardController::class, 'finance']);
});
