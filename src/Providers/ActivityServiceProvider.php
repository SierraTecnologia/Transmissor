<?php

namespace Transmissor\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ActivityServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Nothing to see here
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();

        $loader->alias('Activity', \Transmissor\Facades\Activity::class);

        $this->app->singleton(
            'ActivityService', function ($app) {
                return app(\Transmissor\Services\ActivityService::class);
            }
        );
    }
}
