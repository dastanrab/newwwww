<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    throw new \Exception("این یک خطای تستی است!");
    return view('welcome');
});
