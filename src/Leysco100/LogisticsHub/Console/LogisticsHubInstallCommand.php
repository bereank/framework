<?php

namespace Leysco100\LogisticsHub\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\Shared\Models\FM100;
use Leysco100\Shared\Services\CommonService;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;



class LogisticsHubInstallCommand extends Command
{

    use TenantAware;
    protected $signature = 'leysco100:logistics-hub:install {--tenant=*}';

    protected $description = 'Installing Logistics Hub Package';

    public function handle()
    {

        $menuJsonString = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'MenuItem.Json');
        $menuitems = json_decode($menuJsonString, true);

        DB::connection("tenant")->beginTransaction();
        try {
            $commonService = new CommonService();
            $users = User::all();
            $exist_menu = FM100::where("label","Logistics Hub")->get();
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


    }

}
