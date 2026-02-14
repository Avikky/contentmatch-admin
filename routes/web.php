<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
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

Route::get('clear', function () {
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

Route::redirect('/', '/admin/login');

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (login, OTP)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login']);
    });

    // OTP verification (requires session but not full auth)
    Route::get('otp/verify', [AdminAuthController::class, 'showOtpVerify'])->name('otp.verify');
    Route::post('otp/verify', [AdminAuthController::class, 'verifyOtp']);
    Route::post('otp/resend', [AdminAuthController::class, 'resendOtp'])->name('otp.resend');

    // Authenticated routes
    Route::middleware('admin.auth')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('profile', [ProfileController::class, 'show'])->name('profile');
        Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('profile/upload-image', [ProfileController::class, 'uploadImage'])->name('profile.upload-image');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Admin Management Routes (Super Admin only)
        Route::middleware('admin.super')->prefix('admins')->name('admins.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminManagementController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\AdminManagementController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\AdminManagementController::class, 'store'])->name('store');
            Route::get('/{admin}/edit', [\App\Http\Controllers\Admin\AdminManagementController::class, 'edit'])->name('edit');
            Route::put('/{admin}', [\App\Http\Controllers\Admin\AdminManagementController::class, 'update'])->name('update');
            Route::delete('/{admin}', [\App\Http\Controllers\Admin\AdminManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{admin}/toggle-status', [\App\Http\Controllers\Admin\AdminManagementController::class, 'toggleStatus'])->name('toggle-status');
        });

        // User Management Routes
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
            Route::get('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('show');
            Route::post('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');

            // User Moderation Actions
            Route::post('/{user}/ban', [\App\Http\Controllers\Admin\UserController::class, 'ban'])->name('ban');
            Route::post('/{user}/suspend', [\App\Http\Controllers\Admin\UserController::class, 'suspend'])->name('suspend');
            Route::post('/{user}/reactivate', [\App\Http\Controllers\Admin\UserController::class, 'reactivate'])->name('reactivate');

            // User Related Data
            Route::get('/{user}/reports', [\App\Http\Controllers\Admin\UserController::class, 'reports'])->name('reports');
            Route::get('/{user}/communities', [\App\Http\Controllers\Admin\UserController::class, 'communities'])->name('communities');
            Route::get('/{user}/subscriptions', [\App\Http\Controllers\Admin\UserController::class, 'subscriptions'])->name('subscriptions');
            Route::get('/{user}/moderation-history', [\App\Http\Controllers\Admin\UserController::class, 'moderationHistory'])->name('moderation-history');

            // Community Management
            Route::post('/{user}/remove-from-community', [\App\Http\Controllers\Admin\UserController::class, 'removeFromCommunity'])->name('remove-from-community');
        });

        // Community Management Routes
        Route::prefix('communities')->name('communities.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CommunityController::class, 'index'])->name('index');
            Route::get('/{community}', [\App\Http\Controllers\Admin\CommunityController::class, 'show'])->name('show');
            Route::post('/', [\App\Http\Controllers\Admin\CommunityController::class, 'store'])->name('store');
            Route::post('/{community}', [\App\Http\Controllers\Admin\CommunityController::class, 'update'])->name('update');
            Route::post('/{community}/update-status', [\App\Http\Controllers\Admin\CommunityController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{community}', [\App\Http\Controllers\Admin\CommunityController::class, 'destroy'])->name('destroy');

            // Member Management
            Route::post('/{community}/members/{user}/ban', [\App\Http\Controllers\Admin\CommunityController::class, 'banMember'])->name('members.ban');
            Route::post('/{community}/members/{user}/remove', [\App\Http\Controllers\Admin\CommunityController::class, 'removeMember'])->name('members.remove');
        });

        // Content Management Routes
        Route::prefix('content')->name('content.')->group(function () {
            // Main Content Management
            Route::get('/', [\App\Http\Controllers\Admin\ContentController::class, 'index'])->name('index');
            Route::get('/analytics', [\App\Http\Controllers\Admin\ContentController::class, 'analytics'])->name('analytics');
            Route::get('/reported', [\App\Http\Controllers\Admin\ContentController::class, 'reported'])->name('reported');
            Route::get('/feedback', [\App\Http\Controllers\Admin\ContentController::class, 'feedback'])->name('feedback');
            Route::get('/engagements', [\App\Http\Controllers\Admin\ContentController::class, 'engagements'])->name('engagements');
            Route::get('/export', [\App\Http\Controllers\Admin\ContentController::class, 'export'])->name('export');

            // Content Detail & Actions
            Route::get('/{content}', [\App\Http\Controllers\Admin\ContentController::class, 'show'])->name('show');
            Route::put('/{content}/update-status', [\App\Http\Controllers\Admin\ContentController::class, 'updateStatus'])->name('update-status');
            Route::post('/{content}/ban', [\App\Http\Controllers\Admin\ContentController::class, 'ban'])->name('ban');
            Route::delete('/{content}', [\App\Http\Controllers\Admin\ContentController::class, 'destroy'])->name('destroy');
            // Test route for content status update
            // Bulk Actions
            Route::post('/bulk-update-status', [\App\Http\Controllers\Admin\ContentController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
        });

        // Report Management Routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
            Route::get('/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'show'])->name('show');
            Route::post('/{report}/review', [\App\Http\Controllers\Admin\ReportController::class, 'review'])->name('review');
            Route::post('/{report}/resolve', [\App\Http\Controllers\Admin\ReportController::class, 'resolve'])->name('resolve');
            Route::post('/{report}/dismiss', [\App\Http\Controllers\Admin\ReportController::class, 'dismiss'])->name('dismiss');
            Route::post('/bulk-update', [\App\Http\Controllers\Admin\ReportController::class, 'bulkUpdate'])->name('bulk-update');
        });

        // Analytics & Statistics Routes
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('index');
            Route::get('/users', [\App\Http\Controllers\Admin\AnalyticsController::class, 'users'])->name('users');
            Route::get('/communities', [\App\Http\Controllers\Admin\AnalyticsController::class, 'communities'])->name('communities');
            Route::get('/content', [\App\Http\Controllers\Admin\AnalyticsController::class, 'content'])->name('content');
            Route::get('/engagement', [\App\Http\Controllers\Admin\AnalyticsController::class, 'engagement'])->name('engagement');
        });
    });
});
