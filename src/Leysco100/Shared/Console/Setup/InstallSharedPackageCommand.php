<?php

namespace Leysco100\Shared\Console\Setup;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Models\Shared\Models\FI100;
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
        $oadm = OADM::where("CompnyName",Tenant::current()->name)->first();

        if (!$oadm){
            OADM::create([
                'CompnyName' => Tenant::current()->name
            ]);
        }

        APDI::query()->truncate();
        PDI1::query()->truncate();

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
                'ObjAcronym' => $value['ObjAcronym'] ?? null,
                'DrftObj' => $value['DrftObj'] ?? null
            ]);
            if (array_key_exists('ChildTable', $value) && !empty($value['ChildTable'])) {
                $Row = PDI1::updateOrCreate([
                    'DocEntry' => $Header->id,
                ], [
                    'ChildType' => $value['ChildType'],
                    'ChildTable' => $value['ChildTable'],
                ]);
            }
            if (array_key_exists('children', $value) && !empty($value['children'])) {
                foreach ($value['children'] as $childModel) {

                    $Row = PDI1::updateOrCreate([
                        'DocEntry' => $Header->id,
                        'ChildTable' => $childModel['ChildTable'],

                    ], [
                        'ChildType' => $childModel['ChildType'],
                    ]);
                }
            }
        }

        $this->info("Creating Default User");
        Artisan::call('leysco100:shared:create-default-user --tenant='.Tenant::current()->id);

        $this->info("Creating Default Document Form-settings");
        Artisan::call('leysco100:shared:create-default-document-form-settings --tenant='.Tenant::current()->id);

        $packages = $this->ask("Do You Want To Install All Packages? (Y) yes, (N) No");

        if (strtolower($packages) == "yes" || strtolower($packages) == "y"){
            //install all existing packages
            $this->info("Installing Administration Package");
            Artisan::call('leysco100:administration:install --tenant='.Tenant::current()->id);

            $this->info("Installing Finance Package");
            Artisan::call('leysco100:finance:install --tenant='.Tenant::current()->id);

            $this->info("Installing Inventory Package");
            Artisan::call('leysco100:inventory:install --tenant='.Tenant::current()->id);

            $this->info("Installing Sales Package");
            Artisan::call('leysco100:marketing-documents:install --tenant='.Tenant::current()->id);

            $this->info("Installing Logistics-hub Package");
            Artisan::call('leysco100:logistics-hub:install --tenant='.Tenant::current()->id);

            $this->info("Installing Banking Package");
            Artisan::call('leysco100:banking:install --tenant='.Tenant::current()->id);

            $this->info("Installing Gpm Package");
            Artisan::call('leysco100:gpm:install --tenant='.Tenant::current()->id);

        }
    }
}
