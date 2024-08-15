<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\absensiController;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('absensiControllerAjax', absensiController::class);
