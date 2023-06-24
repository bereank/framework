<?php

namespace Leysco\GatePassManagementModule;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Leysco\GatePassManagementModule\Console\GPMInstallCommand;
use Leysco\GatePassManagementModule\Console\MobileMenuCommand;
use Leysco\GatePassManagementModule\Console\DailyReportCommand;
use Leysco\GatePassManagementModule\Console\InsertFormFieldTypes;
use Leysco\GatePassManagementModule\Console\LeyscoDailyReportsCommand;
use Leysco\GatePassManagementModule\Console\GatePassManagementTestCommand;
use Leysco\GatePassManagementModule\Console\ScanReportCommand;

class GPMModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php", 'gate-pass-management-module');
    }

    public function boot()
    {
        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php" => config_path('gate-pass-management-module.php'),
            ], 'config');

            $this->commands([
                GatePassManagementTestCommand::class,
                GPMInstallCommand::class,
                DailyReportCommand::class,
                LeyscoDailyReportsCommand::class,
                InsertFormFieldTypes::class,
                MobileMenuCommand::class,
                ScanReportCommand::class
            ]);
            $this->publishes([
                __DIR__ . '/../database/migrations/tenant' => database_path('migrations/tenant'),
            ], 'migrations');
        }
        $this->registerRoutes();

        /**
         * Load Migrations And Views
         */
        //    $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'gatepassmanagement');
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
            'prefix' => config('gate-pass-management-module.prefix'),
            'middleware' => config('gate-pass-management-module.middleware'),
        ];
    }
}
