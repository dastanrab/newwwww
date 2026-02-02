<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\Dashboard\Auth\AuthController::class, 'login'])->name('login');
    Route::post('/verify', [\App\Http\Controllers\Api\Dashboard\Auth\AuthController::class, 'verify'])->name('verify');

});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/fava', [DashboardController::class, 'fava']);
    Route::get('/finance', [DashboardController::class, 'finance']);
    Route::get('/clubs', [\App\Http\Controllers\Api\Dashboard\Club\ClubCategoriesController::class, 'index']);
    Route::put('/club/category/{clubCategory}', [\App\Http\Controllers\Api\Dashboard\Club\ClubCategoriesController::class, 'update']);
    Route::post('/club/category', [\App\Http\Controllers\Api\Dashboard\Club\ClubCategoriesController::class, 'store']);
});
