<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $now = Carbon::now();

        // Set logout time to 12:00 noon
        // $logoutTime = $now->copy()->setTime(12, 0);
        $logoutTime = $now->copy()->endOfDay()->setTime(23, 59, 59);

        if ($now->lessThan($logoutTime)) {
            // Calculate remaining minutes until 12 PM
            $minutesLeft = $now->diffInMinutes($logoutTime);
            Config::set('session.lifetime', $minutesLeft);
        } else {
            // If it's already past 12 PM, expire session immediately
            Config::set('session.lifetime', 1);
        }
    }
}
