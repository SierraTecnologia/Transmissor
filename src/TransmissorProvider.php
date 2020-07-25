<?php

namespace Transmissor;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Transmissor\Services\TransmissorService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;

use Log;
use App;
use Config;
use Route;
use Illuminate\Routing\Router;

use Muleta\Traits\Providers\ConsoleTools;

use Transmissor\Facades\Transmissor as TransmissorFacade;
use Illuminate\Contracts\Events\Dispatcher;


class TransmissorProvider extends ServiceProvider
{
    use ConsoleTools;

    const pathVendor = 'sierratecnologia/transmissor';

    public static $aliasProviders = [
        'Transmissor' => \Transmissor\Facades\Transmissor::class,
    ];

    public static $providers = [

        // \Support\SupportProviderService::class,
        \Telefonica\TelefonicaProvider::class,

        
    ];

    /**
     * Rotas do Menu
     */
    public static $menuItens = [

    ];

    /**
     * Alias the services in the boot.
     */
    public function boot()
    {
        
        // Register configs, migrations, etc
        $this->registerDirectories();

        // COloquei no register pq nao tava reconhecendo as rotas para o adminlte
        $this->app->booted(function () {
            $this->routes();
        });

        $this->loadLogger();
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        /**
         * Transmissor; Routes
         */
        Route::group(
            [
                'namespace' => '\Transmissor\Http\Controllers',
                'prefix' => \Illuminate\Support\Facades\Config::get('application.routes.main', ''),
                'as' => 'rica.',
                // 'middleware' => 'rica',
            ], function ($router) {
                include __DIR__.'/../routes/web.php';
            }
        );
    }

    /**
     * Register the services.
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getPublishesPath('config/sitec/transmissor.php'), 'sitec.transmissor');
        

        $this->setProviders();
        // $this->routes();



        // Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->app->singleton(
            'transmissor', function () {
                return new Transmissor();
            }
        );
        
        /*
        |--------------------------------------------------------------------------
        | Register the Utilities
        |--------------------------------------------------------------------------
        */
        /**
         * Singleton Transmissor;
         */
        $this->app->singleton(
            TransmissorService::class, function ($app) {
                Log::channel('sitec-transmissor')->info('Singleton Transmissor;');
                return new TransmissorService(\Illuminate\Support\Facades\Config::get('sitec.transmissor'));
            }
        );

        // Register commands
        $this->registerCommandFolders(
            [
            base_path('vendor/sierratecnologia/transmissor/src/Console/Commands') => '\Transmissor\Console\Commands',
            ]
        );

        // /**
        //  * Helpers
        //  */
        // Aqui noa funciona
        // if (!function_exists('transmissor_asset')) {
        //     function transmissor_asset($path, $secure = null)
        //     {
        //         return route('rica.transmissor.assets').'?path='.urlencode($path);
        //     }
        // }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'transmissor',
        ];
    }

    /**
     * Register configs, migrations, etc
     *
     * @return void
     */
    public function registerDirectories()
    {
        // Publish config files
        $this->publishes(
            [
            // Paths
            $this->getPublishesPath('config/sitec') => config_path('sitec'),
            ], ['config',  'sitec', 'sitec-config']
        );

        // // Publish transmissor css and js to public directory
        // $this->publishes([
        //     $this->getDistPath('transmissor') => public_path('assets/transmissor')
        // ], ['public',  'sitec', 'sitec-public']);

        $this->loadViews();
        $this->loadTranslations();

    }

    private function loadViews()
    {
        // View namespace
        $viewsPath = $this->getResourcesPath('views');
        $this->loadViewsFrom($viewsPath, 'transmissor');
        $this->publishes(
            [
            $viewsPath => base_path('resources/views/vendor/transmissor'),
            ], ['views',  'sitec', 'sitec-views']
        );

    }
    
    private function loadTranslations()
    {
        // Publish lanaguage files
        $this->publishes(
            [
            $this->getResourcesPath('lang') => resource_path('lang/vendor/transmissor')
            ], ['lang',  'sitec', 'sitec-lang', 'translations']
        );

        // Load translations
        $this->loadTranslationsFrom($this->getResourcesPath('lang'), 'transmissor');
    }


    /**
     * 
     */
    private function loadLogger()
    {
        Config::set(
            'logging.channels.sitec-transmissor', [
            'driver' => 'single',
            'path' => storage_path('logs/sitec-transmissor.log'),
            'level' => env('APP_LOG_LEVEL', 'debug'),
            ]
        );
    }

}