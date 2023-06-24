<?php

namespace Leysco100\Shared;

use Illuminate\Console\Command;
use Leysco\LS100SharedPackage\Models\Domains\Shared\Models\APDI;
use Leysco\LS100SharedPackage\Models\Domains\Shared\Models\PDI1;

class InstallSharedPackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:initial_setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'System Initial Setup';

    public function handle()
    {

        $modelstsJsonString = file_get_contents(base_path('resources/setupdata/models.json'));
        $models = json_decode($modelstsJsonString, true);
        foreach ($models as $key => $value) {
            $Header = APDI::updateOrCreate([
                'ObjectID' => $value['ObjectID'],
            ], [
                'ObjectHeaderTable' => $value['ObjectHeaderTable'],
                'PermissionName' => $value['PermissionName'],
                'DocumentName' => $value['DocumentName'],
                'JrnStatus' => $value['JrnStatus'],
                'isDoc' => $value['isDoc'],
            ]);
            $Row = PDI1::updateOrCreate([
                'DocEntry' => $Header->id,

            ], [
                'ChildType' => $value['ChildType'],
                'ChildTable' => $value['ChildTable'],
            ]);

        }

    }
}
