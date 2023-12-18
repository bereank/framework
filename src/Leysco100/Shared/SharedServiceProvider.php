<?php

namespace Leysco100\Shared;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Leysco100\Shared\Console\Setup\DocumentFormSettings;
use Leysco100\Shared\Console\Setup\CreateDefaultUserCommand;
use Leysco100\Shared\Console\Setup\InstallSharedPackageCommand;
use Leysco100\Shared\Console\Setup\RecurringTransactionCommand;

class SharedServiceProvider extends ServiceProvider
{
    public function register()
    {

        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php" => config_path('gpm.php'),
            ], 'config');

            $this->commands([
                InstallSharedPackageCommand::class,
                CreateDefaultUserCommand::class,
                DocumentFormSettings::class,
                RecurringTransactionCommand::class
                
            ]);
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
            $this->loadRoutesFrom(__DIR__ . DIRECTORY_SEPARATOR . "routes" . DIRECTORY_SEPARATOR . "api.php");
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
