<?php

namespace Leysco100\Administration;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Leysco100\Administration\Console\AdministrationMigrationsCommand;
use Leysco100\Administration\Console\CreatePermissions;
use Leysco100\Administration\Console\Alerts\ResetAlertsCommand;
use Leysco100\Administration\Console\BinFieldActivationCommand;
use Leysco100\Administration\Console\Alerts\ProcessAlertsCommand;
use Leysco100\Administration\Console\Setup\ImportNumberingSeries;
use Leysco100\Administration\Console\AdministrationInstallCommand;
use Leysco100\Administration\Console\Setup\CreateUserFormSettings;

class AdministrationServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php", 'administration');
    }



    public function boot()
    {

        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php" => config_path('administration.php'),
            ], 'config');


            $this->commands([
                AdministrationInstallCommand::class,
                AdministrationMigrationsCommand::class,
                CreatePermissions::class,
                ImportNumberingSeries::class,
                CreateUserFormSettings::class,
                BinFieldActivationCommand::class,
                ProcessAlertsCommand::class,
                ResetAlertsCommand::class
            ]);
        }

        // /**
        //  * Load Migrations And Views
        //  */
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/tenant');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'administration');
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
            'prefix' => config('administration.prefix'),
            'middleware' => config('administration.middleware'),
        ];
    }
}
