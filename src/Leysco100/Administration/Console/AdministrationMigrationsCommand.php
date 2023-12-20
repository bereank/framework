<?php

namespace Leysco100\Administration\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Leysco100\Shared\Services\CommonService;
use Leysco100\Shared\Models\Shared\Models\FM100;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;



class AdministrationMigrationsCommand extends Command
{

    use TenantAware;

    protected $signature = 'leysco100:administration:run:migrations {--tenant=*}';

    protected $description = 'Run all Administration Models migration from a set static json';

    public function handle()
    {
//        dd("running migrations");

         $tableName = 'users';
         $jsonsFields = file_get_contents(base_path('resources/setupdata/field_header_footer_details.json'));
         $jsonArray = json_decode($jsonsFields, true);

         foreach ($jsonArray  as $key => $value) {
             $value['fieldType']= "integer";
             Schema::table($tableName, function (Blueprint $table) use ($value,$tableName) {
                 if (!$this->checkIfColumnExist($tableName,$value['FieldName'])) {
                     $table->string($value['FieldName'],$value['FieldSize'])->comment($value['Label']);
                 }
             });
         }
         return Command::SUCCESS;
    }

}
