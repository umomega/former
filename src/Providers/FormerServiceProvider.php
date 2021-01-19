<?php

namespace Umomega\Former\Providers;

use Illuminate\Support\ServiceProvider;

class FormerServiceProvider extends ServiceProvider
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
        $this->publishes([__DIR__ . '/../../resources/lang' => resource_path('lang/vendor/former')], 'lang');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'former');

        $this->loadMigrationsFrom(__DIR__ . '/../../migrations');

        require __DIR__ . '/../Support/helpers.php';
    }

}
