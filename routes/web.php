<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes(['register' => false]);

Route::group(['middleware' => 'auth'], function () {
    // Dashboard
    Route::get('/', [HomeController::class, 'dashboard'])->name('dashboard');

    //Settings
    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
        Route::get('/show', [SettingController::class, 'show'])->name('show');
        Route::post('/list', [SettingController::class, 'list'])->name('list');
        Route::get('/create', [SettingController::class, 'create'])->name('create');
        Route::post('/store', [SettingController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [SettingController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [SettingController::class, 'update'])->name('update');
    });
});
