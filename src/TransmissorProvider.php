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
        [
            'text'        => 'Logs',
            'route'       => 'rica.tracking.larametrics::metrics.index',
            'icon'        => 'dashboard',
            'icon_color'  => 'blue',
            'label_color' => 'success',
            'level'       => 2,
            'order' => 550,
            'section' => "painel",
            // 'access' => \Porteiro\Models\Role::$ADMIN
        ],
        // 'Admin' => [
            [
                'text'        => 'Notifications',
                'route'       => 'admin.transmissor.notifications.index',
                'icon'        => 'fas fa-fw fa-envelope',
                'section'     => 'admin',
                'order' => 2101,
                'level'       => 2,
            ],
        // ],
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
        $this->mergeConfigFrom($this->getPublishesPath('config'.DIRECTORY_SEPARATOR.'sitec'.DIRECTORY_SEPARATOR.'transmissor.php'), 'sitec.transmissor');
        

        $this->setProviders();

        // $kernel = $this->app->make(Kernel::class);
        // $kernel->pushMiddleware(TransmissorCallbacks::class);


        // Register Migrations
        $this->loadMigrationsFrom(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations');

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
            ['views',  'sitec', 'sitec-views', 'transmissor-views']
        );
    }
    
    private function loadTranslations()
    {
        // Publish lanaguage files
        $this->publishes(
            [
            $this->getResourcesPath('lang') => resource_path('lang'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'transmissor')
            ],
            ['lang',  'sitec', 'sitec-lang', 'translations', 'transmissor-lang']
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
            'path' => storage_path('logs'.DIRECTORY_SEPARATOR.'sitec-transmissor.log'),
            'level' => env('APP_LOG_LEVEL', 'debug'),
            ]
        );
    }
}
// /**
//  * <?php
// Antigo Provider Messenger

// namespace Cmgmyr\Messenger;

// use Transmissor\Models\Messenger\Message;
// use Transmissor\Models\Messenger\Models;
// use Transmissor\Models\Messenger\Participant;
// use Transmissor\Models\Messenger\Thread;
// use Illuminate\Support\ServiceProvider;

// class MessengerServiceProvider extends ServiceProvider
// {
//     /**
//      * Bootstrap the application services.
//      *
//      * @return void
//      */
//     public function boot()
//     {
//         $this->offerPublishing();
//         $this->setMessengerModels();
//         $this->setUserModel();
//     }

//     /**
//      * Register the application services.
//      *
//      * @return void
//      */
//     public function register()
//     {
//         $this->configure();
//     }

//     /**
//      * Setup the configuration for Messenger.
//      *
//      * @return void
//      */
//     protected function configure()
//     {
//         $this->mergeConfigFrom(
//             base_path('vendor/cmgmyr/messenger/config/config.php'),
//             'messenger'
//         );
//     }

//     /**
//      * Setup the resource publishing groups for Messenger.
//      *
//      * @return void
//      */
//     protected function offerPublishing()
//     {
//         if ($this->app->runningInConsole()) {
//             $this->publishes([
//                 base_path('vendor/cmgmyr/messenger/config/config.php') => config_path('messenger.php'),
//             ], 'config');

//             $this->publishes([
//                 base_path('vendor/cmgmyr/messenger/migrations') => base_path('database/migrations'),
//             ], 'migrations');
//         }
//     }

//     /**
//      * Define Messenger's models in registry.
//      *
//      * @return void
//      */
//     protected function setMessengerModels()
//     {
//         $config = $this->app->make('config');

//         Models::setMessageModel($config->get('messenger.message_model', Message::class));
//         Models::setThreadModel($config->get('messenger.thread_model', Thread::class));
//         Models::setParticipantModel($config->get('messenger.participant_model', Participant::class));

//         Models::setTables([
//             'messages' => $config->get('messenger.messages_table', Models::message()->getTable()),
//             'participants' => $config->get('messenger.participants_table', Models::participant()->getTable()),
//             'threads' => $config->get('messenger.threads_table', Models::thread()->getTable()),
//         ]);
//     }

//     /**
//      * Define User model in Messenger's model registry.
//      *
//      * @return void
//      */
//     protected function setUserModel()
//     {
//         $config = $this->app->make('config');

//         $model = $config->get('messenger.user_model', function () use ($config) {
//             return $config->get('auth.providers.users.model', $config->get('auth.model'));
//         });

//         Models::setUserModel($model);

//         Models::setTables([
//             'users' => (new $model)->getTable(),
//         ]);
//     }
// }