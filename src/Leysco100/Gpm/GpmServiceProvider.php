<?php

namespace Leysco100\Gpm;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Leysco100\Gpm\Console\GPMInstallCommand;
use Leysco100\Gpm\Console\MobileMenuCommand;
use Leysco100\Gpm\Console\ScanReportCommand;
use Leysco100\Gpm\Console\DailyReportCommand;
use Leysco100\Gpm\Console\InsertFormFieldTypes;
use Leysco100\Gpm\Console\SyncedLaterDocsCommand;
use Leysco100\Gpm\Console\LeyscoDailyReportsCommand;
use Leysco100\Gpm\Console\GatePassManagementTestCommand;



class GpmServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php", 'gpm');
    }

    public function boot()
    {
  
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php" => config_path('gpm.php'),
            ], 'config');

            $this->commands([
                GPMInstallCommand::class,
                DailyReportCommand::class,
                LeyscoDailyReportsCommand::class,
                InsertFormFieldTypes::class,
                MobileMenuCommand::class,
                ScanReportCommand::class,
                SyncedLaterDocsCommand::class
            ]);
         
        }
    

        /**
         * Load Migrations And Views
         */
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/tenant');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'gpm');

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
