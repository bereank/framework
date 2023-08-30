<?php


namespace Leysco100\Gpm\Http\Controllers\API;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Leysco100\Gpm\Http\Controllers\Controller;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\MarketingDocuments\Models\FieldsTemplateRows;
use Leysco100\Shared\Models\MarketingDocuments\Models\FormFieldsTemplate;



class FieldsTemplateController extends Controller
{

    /**
     *
     */

    public function index()
    {
        try {
            $data = FormFieldsTemplate::get();

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Get Scan Logs
     */
    public function store(Request $request)
    {
        try {
            $id = Auth::user()->id;
            $validatedData = $request->validate([
                // 'ObjectType' => 'nullable|exists:a_p_d_i_s,id',
                'ObjectType'=>'nullable',
                'Enabled' => 'nullable|boolean',
                'FormFields' => 'nullable|array',
                'DefaultTemplate' => 'nullable',
                'Name' => 'required',
            ]);
            if ($validatedData['DefaultTemplate']) {
                FormFieldsTemplate::where('DefaultTemplate', '!=', null)
                    ->update([
                        'DefaultTemplate' => null,
                    ]);
            }
            $data = FormFieldsTemplate::create([
                'ObjectType' => $validatedData['ObjectType'] ?? null,
                'UserSign' => $id,
                'DefaultTemplate' => $validatedData['DefaultTemplate'],
                'Name' => $validatedData['Name'],
                'Enabled' => $validatedData['Enabled'] ?? 0,
            ]);

            if (!empty($validatedData['FormFields'])) {

            foreach ($validatedData['FormFields'] as $field) {
                FieldsTemplateRows::create([
                    'FormFieldId' => $field,
                    'TemplateId' =>  $data->id,
                ]);
            }
        }
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Get Scan Logs
     */
    public function show($id)
    {
        try {
            $template = FormFieldsTemplate::find($id);
            $form_fields = DB::connection('tenant')->table('form_fields_templates')
                ->join('fields_template_rows', 'fields_template_rows.TemplateId', '=', 'form_fields_templates.id')
                ->join('form_fields', 'form_fields.id', '=', 'fields_template_rows.FormFieldId')
                ->join('form_field_types', 'form_field_types.id', '=', 'form_fields.type_id')
                ->where('form_fields_templates.id', '=', $id)
                ->select('form_fields.id', 'form_fields.key', 'form_fields.indexno', 'form_fields.title', 'form_field_types.Name as type', 'form_fields.mandatory');
            $res = collect();
            foreach ($form_fields->get() as $field) {
                if ($field->type == "Dropdown") {
                    $values = DB::connection('tenant')->table('form_field_values')
                        ->where('field_id', '=', $field->id)
                        ->select('id', 'Value')
                        ->get();
                    $field->values = $values;
                }
                $res->push($field);
            }
            $resp['template'] = $template;
            $resp['fields'] = $res;


            return (new ApiResponseService())->apiSuccessResponseService($resp);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Get Scan Logs
     */
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'ObjectType' => 'nullable',
                'Enabled' => 'nullable|boolean',
                'FormFields' => 'nullable|array',
                'Name' => 'required',
                'DefaultTemplate' => 'nullable',
            ]);
            $userid = Auth::user()->id;
            $data = FormFieldsTemplate::findOrFail($id);

            if ($validatedData['DefaultTemplate']) {
                FormFieldsTemplate::where('DefaultTemplate', '!=', null)
                    ->update([
                        'DefaultTemplate' => null,
                    ]);
            }

            $data->update([
                'ObjectType' => $validatedData['ObjectType'] ?? null,
                'UserSign' => $userid,
                'DefaultTemplate' => $validatedData['DefaultTemplate'] ?? null,
                'Name' => $validatedData['Name'],
                'Enabled' => $validatedData['Enabled'],
            ]);
            $templateRows = FieldsTemplateRows::where('TemplateId', $data->id);
            $templateRows->delete();
            if (!empty($validatedData['FormFields'])) {
            foreach ($validatedData['FormFields'] as $field) {

                FieldsTemplateRows::create([
                    'FormFieldId' => $field,
                    'TemplateId' =>  $data->id,
                ]);
            }
        }
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}