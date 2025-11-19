<?php

namespace Sajidifti\ManagementPanel;

use Illuminate\Support\ServiceProvider;

class ManagementPanelServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Load helpers
        $helpersPath = __DIR__.'/helpers.php';
        if (file_exists($helpersPath)) {
            require_once $helpersPath;
        }
        
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__.'/../config/management.php', 'management'
        );
    }

    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/management.php');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'management-panel');
        
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/management.php' => config_path('management.php'),
        ], 'management-panel-config');
        
        // Publish only the stable built assets (app.css + app.js)
        $publicVendorPath = public_path('vendor/management-panel');
        $this->publishes([
            __DIR__.'/../public/css/app.css' => $publicVendorPath . '/css/app.css',
            __DIR__.'/../public/js/app.js' => $publicVendorPath . '/js/app.js',
        ], 'management-panel-assets');
        
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\CleanSessionsCommand::class,
            ]);
        }
        
        // Register middleware
        $router = $this->app['router'];
        $router->aliasMiddleware('management.session', Http\Middleware\ManagementSessionHandler::class);
        $router->aliasMiddleware('management.auth', Http\Middleware\ManagementAuth::class);
        $router->aliasMiddleware('management.guest', Http\Middleware\RedirectIfManagementAuthenticated::class);
        $router->aliasMiddleware('management.errors', Http\Middleware\ShareManagementErrors::class);
    }
}
