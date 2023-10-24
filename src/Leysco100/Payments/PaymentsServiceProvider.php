<?php

namespace Leysco100\Payments;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class PaymentsServiceProvider extends ServiceProvider
{
    public function register()
    {

        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . DIRECTORY_SEPARATOR . "config" . 
                DIRECTORY_SEPARATOR . "config.php" => config_path('payments.php'),
            ], 'config');
        }
    }

    public function boot()
    {

        
        /**
         * Load Migrations And Views
         */
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/tenant');
        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . DIRECTORY_SEPARATOR . "routes" . 
            DIRECTORY_SEPARATOR . "api.php");
        });
    }

    protected function routeConfiguration()
    {
        return [
            'prefix' => config('gpm.prefix'),
            'middleware' => config('gpm.middleware'),
        ];
    }
}
