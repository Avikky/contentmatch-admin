<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CommunityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('clear', function(){
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    Artisan::call('view:clear');
    return 'ok';
});

// Email Preview Route (for development/testing)
// Route::get('preview/password-reset-email', function() {
//     return new \App\Mail\PasswordResetMail('John Doe', 'TempPass123!');
// })->name('preview.password-reset-email');

Route::redirect('/', '/admin/dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('/dashboard', '/admin/dashboard')->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('communities', CommunityController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('admins', [AdminController::class, 'index'])->name('admins.index');
        Route::post('admins', [AdminController::class, 'store'])->name('admins.store');
        Route::delete('admins/{user}', [AdminController::class, 'destroy'])->name('admins.destroy');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

        Route::get('profile', [AdminProfileController::class, 'show'])->name('profile.show');
        Route::put('profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
    });
});

require __DIR__.'/auth.php';