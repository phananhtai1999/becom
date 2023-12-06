<?php

namespace App\Providers;

use App\Guards\HeaderGuard;
use Illuminate\Support\ServiceProvider;

class CustomHeaderGuardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['auth']->extend('x-header', function ($app, $name, array $config) {
            return new HeaderGuard($app['request']);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
