<?php

namespace Leysco100\Administration\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Leysco100\Shared\Services\CommonService;
use Leysco100\Shared\Models\Shared\Models\FM100;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;



class AdministrationInstallCommand extends Command
{

    use TenantAware;
    protected $signature = 'leysco100:administration:install {--tenant=*}';

    protected $description = 'Installing Administration Package';

    public function handle()
    {

        //Step one: create user navigation menu
        $menuJsonString = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'MenuItem.Json');
        $menuitems = json_decode($menuJsonString, true);
       (new CommonService())->createOrUpdateMenu($menuitems);

        //Step two: Run all migrations associated to Admin Module

      //  Artisan::call("leysco100:administration:run:migrations {--tenant=*}");
    }

}
