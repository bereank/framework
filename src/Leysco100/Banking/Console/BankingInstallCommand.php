<?php

namespace Leysco100\Banking\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Services\CommonService;
use Leysco100\Shared\Models\Shared\Models\FM100;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;



class BankingInstallCommand extends Command
{

    use TenantAware;
    protected $signature = 'leysco100:banking:install {--tenant=*}';

    protected $description = 'Installing Banking Package';

    public function handle()
    {

        $menuJsonString = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'MenuItem.json');
        $menuitems = json_decode($menuJsonString, true);

        DB::connection("tenant")->beginTransaction();
        try {
            $commonService = new CommonService();
            $users = User::all();
            $exist_menu = FM100::where("label","Banking")->get();
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