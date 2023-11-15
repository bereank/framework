<?php

namespace Leysco100\MarketingDocuments\Console;

use Illuminate\Console\Command;
use Leysco100\Shared\Actions\Helpers\CreateUDFHelperAction;
use Leysco100\Shared\Models\FormSetting\Models\FM100;
use Leysco100\Shared\Models\MarketingDocuments\Models\ODRF;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;



class MaketingDocumentsUdfsCreateCommand extends Command
{

    use TenantAware;

    protected $signature = 'leysco100:marketing-documents:create-udfs {--tenant=*}';

    protected $description = 'Create Udfs from existing marketing documents';

    public function handle()
    {
        //get all Udfs from odrf table

        $model = new ODRF();
        $columns = $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());

        foreach ($columns as $column){
            if (strpos($column, "U_") === 0) {
                $userFields[] = str_replace("U_", "", $column);
            }
        }

        $tables = APDI::with('pdi1')->where('DocType', 1)->get();

        foreach ($tables as $table) {
            $objType = $table->ObjectID;
            $tableName = (new $table->ObjectHeaderTable)->getTable();
            foreach ( $userFields as $key =>  $userField) {
                $fieldName =  $userField;
                $fieldDescription =  "Test Description";
                $fieldType =  "string";
                $fieldSize =  255;
                (new CreateUDFHelperAction($tableName, $fieldName, $fieldDescription, $fieldType, $fieldSize,$objType))->handle();
            }

        }

    }

}
