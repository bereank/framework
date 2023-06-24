<?php

namespace Leysco100\Gpm;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Leysco100\Gpm\Console\GPMInstallCommand;
use Leysco100\Gpm\Console\MobileMenuCommand;
use Leysco100\Gpm\Console\ScanReportCommand;
use Leysco100\Gpm\Console\DailyReportCommand;
use Leysco100\Gpm\Console\InsertFormFieldTypes;
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
        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php" => config_path('ggpm.php'),
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
         
        }
    

        /**
         * Load Migrations And Views
         */
        //    $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'gatepassmanagement');
    }


}
