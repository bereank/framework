<?php

namespace Leysco100\Shared\Console\Setup;

use Illuminate\Console\Command;
use Spatie\Multitenancy\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\Shared\Models\PDI1;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;
use Leysco100\Shared\Models\Administration\Models\OADM;


class InstallSharedPackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    use TenantAware;
    protected $signature = 'leysco100:shared:initial_setup {--tenant=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'System Initial Setup';

    public function handle()
    {


//        OADM::create([
//                'CompnyName' => Tenant::current()->name
//        ]);
        
        $modelsJsonString = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'models.json');
       
        $models = json_decode($modelsJsonString, true);
        foreach ($models as $key => $value) {
            $Header = APDI::updateOrCreate([
                'ObjectID' => $value['ObjectID'],
            ], [
                'ObjectHeaderTable' => $value['ObjectHeaderTable'],
                'PermissionName' => $value['PermissionName'],
                'DocumentName' => $value['DocumentName'],
                'JrnStatus' => $value['JrnStatus'],
                'isDoc' => $value['isDoc'],
                'ObjAcronym'=>$value['ObjAcronym']??null
            ]);
            if(array_key_exists('ChildTable',$value) && !empty($value['ChildTable'])){
                $Row = PDI1::updateOrCreate([
                    'DocEntry' => $Header->id,
                ], [
                    'ChildType' => $value['ChildType'],
                    'ChildTable' => $value['ChildTable'],
                ]);
            }
           

        }


//        $this->info("Creating Default User");
//        Artisan::call('leysco100:shared:create-default-user');

    }
}
