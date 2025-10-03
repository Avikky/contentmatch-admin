<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
 
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

    Route::post('login', [LoginController::class, 'login']);

    Route::get('forgot-password', [ResetPasswordController::class, 'showResetForm'])
                ->name('forgot-password');

    Route::post('forgot-password', [ResetPasswordController::class, 'reset'])
                ->name('password.email');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])
                ->name('logout');
});
