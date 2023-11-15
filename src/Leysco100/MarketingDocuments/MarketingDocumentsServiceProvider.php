<?php

namespace Leysco100\MarketingDocuments;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Leysco100\BusinessPartner\Console\BusinessPartnerInstallCommand;
use Leysco100\MarketingDocuments\Console\MaketingDocumentsInstallCommand;
use Leysco100\MarketingDocuments\Console\MaketingDocumentsUdfsCreateCommand;


class MarketingDocumentsServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php", 'marketing-documents');
    }



    public function boot()
    {

        // Register the command if we are using the application via the CLI
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php" => config_path('marketing-documents.php'),
            ], 'config');


            $this->commands([
                MaketingDocumentsInstallCommand::class,
                MaketingDocumentsUdfsCreateCommand::class
            ]);
        }

        // /**
        //  * Load Migrations And Views
        //  */
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
            'prefix' => config('administration.prefix'),
            'middleware' => config('administration.middleware'),
        ];
    }
}
