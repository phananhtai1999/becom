<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Response::macro('attachment', function ($content) {

            $headers = [
                'Content-type'        => 'text/plain',
                'Content-Disposition' => 'attachment; filename="datafile.json"',
            ];

            return Response::make($content, 200, $headers);
        });
    }
}
