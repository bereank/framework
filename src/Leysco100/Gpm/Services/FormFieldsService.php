<?php

namespace Leysco100\Gpm\Services;

use Illuminate\Support\Facades\DB;
use Leysco100\Shared\Models\MarketingDocuments\Models\FormFieldsTemplate;



class FormFieldsService
{

    public function getFormFields()
    {
        // if ($mode == 1) {
        $isBackupMode = (new BackupModeService())->isBackupMode();

        if ($isBackupMode) {
            $template  = FormFieldsTemplate::where('id', $isBackupMode->FieldsTemplate)->first();
        } else {
            $template  = FormFieldsTemplate::where('DefaultTemplate', true)->first();
        }
        // } else {
        //     $template  = FormFieldsTemplate::where('DefaultTemplate', true)->first();
        // }



        $form_fields = DB::connection('tenant')->table('form_fields_templates')
            ->join('fields_template_rows', 'fields_template_rows.TemplateId', '=', 'form_fields_templates.id')
            ->join('form_fields', 'form_fields.id', '=', 'fields_template_rows.FormFieldId')
            ->join('form_field_types', 'form_field_types.id', '=', 'form_fields.type_id')
            ->where('form_fields_templates.id', '=', $template->id)
            ->select('form_fields.id', 'form_fields.key', 'form_fields.indexno', 'form_fields.title', 'form_field_types.Name as type', 'form_fields.mandatory as Mandatory');
        $res = collect();
        foreach ($form_fields->get() as $field) {
            if ($field->type == "Dropdown") {
                $values = DB::connection('tenant')->table('form_field_values')
                    ->where('field_id', '=', $field->id)
                    ->select('id', 'Value as Name')
                    ->get();
                $field->values = $values;
            }
            $field->content = '';
            $res->push($field);
        }
        $resp['template'] = $template;
        $resp['fields'] = $res;
        return   $resp['fields'];
    }
}
