<?php

namespace Leysco100\Administration\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Leysco100\Shared\Models\Administration\Models\User;
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
        $menuJsonString = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'MenuItem.Json');
        $menuitems = json_decode($menuJsonString, true);
        DB::connection("tenant")->beginTransaction();
        try {
            $commonService = new CommonService();
            $users = User::all();
            $exist_menu = FM100::where("label","Administration")->get();
            if(count($exist_menu)>0){
                $commonService->deleteExistingMenu($exist_menu);
            }
            foreach ($users as $user){
                $commonService->createOrUpdateMenu($menuitems, null, $user->id);
            }
            DB::connection("tenant")->commit();
        }catch (\Throwable $th){
            DB::connection("tenant")->rollBack();
        }

        //Step two: Run all migrations associated to Admin Module

      //  Artisan::call("leysco100:administration:run:migrations {--tenant=*}");
    }

}
