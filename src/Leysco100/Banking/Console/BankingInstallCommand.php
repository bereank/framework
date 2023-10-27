<?php

namespace Leysco100\Banking\Console;

use Illuminate\Console\Command;
use Leysco100\Shared\Services\CommonService;
use Leysco100\Shared\Models\FormSetting\Models\FM100;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;



class BankingInstallCommand extends Command
{

    use TenantAware;
    protected $signature = 'leysco100:banking:install {--tenant=*}';

    protected $description = 'Installing Banking Package';

    public function handle()
    {

   

        $menuJsonString = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'MenuItem.Json');
        $menuitems = json_decode($menuJsonString, true);

        (new CommonService())->createOrUpdateMenu($menuitems);
    }
}