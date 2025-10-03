<?php

namespace App\Providers;

use App\Services\LoginTrailService;
use Illuminate\Support\ServiceProvider;

class LoginTrailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('login-trail', function ($app) {
            return new LoginTrailService();
        });

        $this->app->singleton(LoginTrailService::class, function ($app) {
            return $app['login-trail'];
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}