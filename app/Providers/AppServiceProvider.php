<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;
use Illuminate\Support\Facades\Log;
use App\Services\DatabaseLoader;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->singleton('translation.loader', function($app) {
        //     return new DatabaseLoader($app['files'], $app['path.lang']);
        // });

        // $this->app->extend('translator', function($service, $app) {
        //     return new Translator($app['translation.loader'], $app['config']['app.locale']);
        // });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
