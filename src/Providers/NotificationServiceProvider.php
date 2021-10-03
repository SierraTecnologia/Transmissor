<?php

namespace Transmissor\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
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

        $loader->alias('Notifications', \Transmissor\Facades\Notifications::class);

        $this->app->singleton(
            'NotificationService', function ($app) {
                return app(\Transmissor\Services\NotificationService::class);
            }
        );
    }
}
