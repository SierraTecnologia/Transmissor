<?php

namespace Transmissor;

use App;
use Config;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\AliasLoader;

use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Log;

use Muleta\Traits\Providers\ConsoleTools;

use Route;

use Transmissor\Facades\Transmissor as TransmissorFacade;
use Transmissor\Observers\TransmissorCallbacks;
use Transmissor\Services\TransmissorService;

class TransmissorProvider extends ServiceProvider
{
    use ConsoleTools;

    public $packageName = 'transmissor';
    const pathVendor = 'sierratecnologia/transmissor';

    public static $aliasProviders = [
        'Transmissor' => \Transmissor\Facades\Transmissor::class,
    ];

    public static $providers = [

        // \Support\SupportProviderService::class,
        \Telefonica\TelefonicaProvider::class,
        \Transmissor\Providers\NotificationServiceProvider::class,
        \Transmissor\Providers\ActivityServiceProvider::class,

        
    ];

    /**
     * Rotas do Menu
     */
    public static $menuItens = [
        'Admin' => [
            [
                'text'        => 'Notifications',
                'route'       => 'admin.transmissor.notifications.index',
                'icon'        => 'puzzle-piece',
                'section'     => 'painel',
                'level'       => 3,
            ],
        ],
    ];

    /**
     * Alias the services in the boot.
     */
    public function boot()
    {
        
        // Register configs, migrations, etc
        $this->registerDirectories();

        // COloquei no register pq nao tava reconhecendo as rotas para o adminlte
        $this->app->booted(
            function () {
                $this->routes();
            }
        );

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
        $this->loadRoutesForRiCa(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'routes');
    }

    /**
     * Register the services.
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getPublishesPath('config/sitec/transmissor.php'), 'sitec.transmissor');
        

        $this->setProviders();

        // $kernel = $this->app->make(Kernel::class);
        // $kernel->pushMiddleware(TransmissorCallbacks::class);


        // Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->app->singleton(
            'transmissor',
            function () {
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
            TransmissorService::class,
            function ($app) {
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
            $this->getPublishesPath('config'.DIRECTORY_SEPARATOR.'sitec') => config_path('sitec'),
            ],
            ['config',  'sitec', 'sitec-config']
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
            $viewsPath => base_path('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'transmissor'),
            ],
            ['views',  'sitec', 'sitec-views']
        );
    }
    
    private function loadTranslations()
    {
        // Publish lanaguage files
        $this->publishes(
            [
            $this->getResourcesPath('lang') => resource_path('lang'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'transmissor')
            ],
            ['lang',  'sitec', 'sitec-lang', 'translations']
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
            'logging.channels.sitec-transmissor',
            [
            'driver' => 'single',
            'path' => storage_path('logs'.DIRECTORY_SEPARACTOR.'sitec-transmissor.log'),
            'level' => env('APP_LOG_LEVEL', 'debug'),
            ]
        );
    }
}
