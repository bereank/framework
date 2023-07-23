<?php

namespace Leysco100\Administration;

use Illuminate\Support\ServiceProvider;


class AdministrationServiceProvider extends ServiceProvider
{
    public function register()
    {

          // Register the command if we are using the application via the CLI
          if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php" => config_path('ggpm.php'),
            ], 'config');

            $this->commands([
                InstallSharedPackageCommand::class,
                CreateDefaultUserCommand::class
            ]);
         
        }
      
    }

    public function boot()
    {


        
        /**
         * Load Migrations And Views
         */
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/tenant');
   
    }
}
