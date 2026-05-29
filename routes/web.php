<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TopController::class, 'index'])->name('top');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::resource('accounts', AccountController::class)
    ->middleware('auth');

// Breezeが生成した登録・ログイン・ログアウト用ルート
require __DIR__.'/auth.php';
