<?php

namespace Leysco100\Shared\Console\Setup;

use Illuminate\Console\Command;
use Leysco100\Shared\Models\Shared\Models\FI100;
use Leysco100\Shared\Models\Shared\Models\FM100;
use Leysco100\Shared\Models\Shared\Models\FT100;
use Leysco100\Shared\Models\Shared\Models\FTR100;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class DocumentFormSettings extends Command
{
    use TenantAware;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leysco100:shared:create-default-document-form-settings {--tenant=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup initial Document Form Settings';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $allInputs = FI100::query()->truncate();
        $allTableData = FT100::query()->truncate();
        $allTabsData = FTR100::query()->truncate();

        // Form Settings
        $allDocuments = APDI::get();
        foreach ($allDocuments as $key => $form) {
            //Creating Form Header Fields
            $fieldsJsonString = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'fields.json');
            $fields = json_decode($fieldsJsonString, true);
            foreach ($fields as $key => $value) {
                $document = FI100::firstOrCreate([
                    'FormID' => $form->id,
                    'FieldName' => $value['FieldName'],
                    'Label' => $value['Label'],
                    'FieldType' => $value['FieldType'],
                    'ColumnWidth' => $value['ColumnWidth'],
                    'Visible' => $value['Visible'],
                    'Readonly' => $value['Readonly'],
                    'Required' => $value['Required'],
                    "data" => $value['data'],
                    "Location" => $value['Location'],
                    "Position" => $value['Position'] ?? null,
                    'TabID' => $value['TabID'],
                    'ItemValue' => array_key_exists('ItemValue', $value) ? $value['ItemValue'] : null,
                    'ItemText' => array_key_exists('ItemText', $value) ? $value['ItemText'] : null,
                    'ClickEvent' => array_key_exists('ClickEvent', $value) ? $value['ClickEvent'] : null,
                ]);
            }
            //Creating Tab for Each Form
            $formTabsJsonString = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tabs.json');
            $form_tabs = json_decode($formTabsJsonString, true);
            foreach ($form_tabs as $key => $value) {
                $newtab = FT100::firstOrCreate([
                    'FormID' => $form->id,
                    'Label' => $value['Label'],
                    'WithTable' => $value['WithTable'],
                ]);

                if ($newtab->WithTable == 1) {
                    $uomsJsonString = file_get_contents(__DIR__. DIRECTORY_SEPARATOR.'rows_fields.json');
                    $tablerows = json_decode($uomsJsonString, true);
                    foreach ($tablerows as $key => $tablerow) {
                        $table = FTR100::firstOrCreate([
                            'FormID' => $form->id,
                            'text' => $tablerow['text'],
                            'value' => $tablerow['value'],
                            'FieldType' => $tablerow['FieldType'],
                            'ColumnWidth' => $tablerow['ColumnWidth'],
                            "data" => $tablerow['data'],
                            "itemText" => $tablerow['itemText'],
                            "itemValue" => $tablerow['itemValue'],
                            'width' => $tablerow['width'],
                            'Visible' => $tablerow['Visible'],
                            'modalVisible' => $tablerow['modalVisible'],
                            'readonly' => $tablerow['readonly'],
                            'TabID' => $newtab->id,
                            'ClickEvent' => array_key_exists('ClickEvent', $tablerow) ? $tablerow['ClickEvent'] : null,
                        ]);
                    }
                }
            }
        }
    }
}
