<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\Dashboard\Auth\AuthController::class, 'login'])->name('login');
    Route::post('/verify', [\App\Http\Controllers\Api\Dashboard\Auth\AuthController::class, 'verify'])->name('verify');

});
Route::middleware([])->group(function () {
    Route::prefix('setting')->group(function () {
        Route::prefix('polygons')->group(function () {
           Route::get('/', [DashboardController::class, 'polygons']);
            Route::get('/{polygon}', function (\App\Models\Polygon $polygon) {
                Auth::loginUsingId(5);
                if (!Gate::allows('stat_submit_division_excel',\auth()->user())) {
                    return response()->json([
                        'message' => 'Unauthorized'
                    ], 403);
                }
                return response()->json([$polygon]);
            });
        });
    });
    Route::get('/fava', [DashboardController::class, 'fava']);
    Route::get('/finance', [DashboardController::class, 'finance']);
    Route::get('/clubs', [\App\Http\Controllers\Api\Dashboard\Club\ClubCategoriesController::class, 'index']);
    Route::put('/club/category/{clubCategory}', [\App\Http\Controllers\Api\Dashboard\Club\ClubCategoriesController::class, 'update']);
    Route::post('/club/category', [\App\Http\Controllers\Api\Dashboard\Club\ClubCategoriesController::class, 'store']);
});
