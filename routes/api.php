<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Login Trail API Routes
Route::middleware('auth:sanctum')->prefix('login-trails')->name('api.login-trails.')->group(function () {
    Route::get('/', [\App\Http\Controllers\LoginTrailController::class, 'apiIndex'])->name('index');
    Route::get('user/{userId}/activity', [\App\Http\Controllers\LoginTrailController::class, 'userActivity'])->name('user.activity');
    Route::get('failed-attempts', [\App\Http\Controllers\LoginTrailController::class, 'failedAttempts'])->name('failed-attempts');
    Route::get('statistics', [\App\Http\Controllers\LoginTrailController::class, 'statistics'])->name('statistics');
});

// Route::post('branch', [HomeController::class, 'branch']);
// Route::post('users', [HomeController::class, 'users']);

// Route::get('staff/{query}', [HomeController::class, 'fetchstaff']);