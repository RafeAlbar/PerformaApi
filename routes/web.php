<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\absensiController;
use App\Http\Controllers\absensiCoController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\rekapController;
use App\Http\Controllers\dashboardControllerController;
use App\Http\Controllers\loginController;

// Route::get('/', function () {
//     return view('welcome');
// });



Route::resource('absensiControllerAjax', absensiController::class);

Route::resource('absensiCoControllerAjax', absensiCoController::class);




Route::get('/rekap', [rekapController::class, 'index'])->name('rekap.index');
Route::get('/get-data', [rekapController::class, 'getData'])->name('rekap.getData');

Route::post('/rekap/checkin', [RekapController::class, 'storeCheckin'])->name('rekap.storeCheckin');
Route::post('/rekap/checkout', [RekapController::class, 'storeCheckout'])->name('rekap.storeCheckout');



Route::get('/', [dashboardController::class, 'index'])->name('dashboard.index');

Route::get('/data-table1', [dashboardController::class, 'getTable1Data'])->name('data.table1');
Route::get('/data-table2', [dashboardController::class, 'getTable2Data'])->name('data.table2');

Route::get('/login', [loginController::class, 'loginForm'])->name('login');
Route::post('/sesi/login', [loginController::class, 'authenticate']);
