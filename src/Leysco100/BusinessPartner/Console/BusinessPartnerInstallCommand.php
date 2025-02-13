<?php

namespace Leysco100\BusinessPartner\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\Shared\Models\FM100;
use Leysco100\Shared\Services\CommonService;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;



class BusinessPartnerInstallCommand extends Command
{

    use TenantAware;
    protected $signature = 'leysco100:business-partner:install {--tenant=*}';

    protected $description = 'Installing Business Partner Package';

    public function handle()
    {

   

        $menuJsonString = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'MenuItem.Json');
        $menuitems = json_decode($menuJsonString, true);

        DB::connection("tenant")->beginTransaction();
        try {
            $commonService = new CommonService();
            $users = User::all();
            $exist_menu = FM100::where("label","Business Partners")->get();
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
