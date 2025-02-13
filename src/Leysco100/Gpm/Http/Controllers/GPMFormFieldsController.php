<?php

namespace Leysco100\Gpm\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Leysco100\Shared\Models\Gpm\Models\FormField;
use Leysco100\Shared\Models\Gpm\Models\FormFieldType;
use Leysco100\Shared\Models\Gpm\Models\FormFieldValue;
use Leysco100\Shared\Models\MobileNavBar;
use Leysco100\Shared\Services\ApiResponseService;


class GPMFormFieldsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $formFields = FormField::with(['type', 'dropDownValues'])
                ->get();
            return (new ApiResponseService())->apiSuccessResponseService($formFields);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function  getFieldTypes()
    {
        try {
            $formFieldType = FormFieldType::get();
            return (new ApiResponseService())->apiSuccessResponseService($formFieldType);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {



        try {

            $validatedData = $request->validate([
                'key' => 'nullable|string|max:255',
                'indexno' => 'nullable|integer',
                'title' => 'required|string|max:255',
                'type_id' => 'required|max:255',
                'mandatory' => 'nullable|boolean',
                'status' => 'boolean',
                'drop_down_values' => 'nullable|array',
                'drop_down_values.*.Value' => 'nullable|string|max:255',

            ]);

            if ($validatedData['type_id'] == 4 && !Arr::has($validatedData, 'drop_down_values')) {
                return (new ApiResponseService())->apiFailedResponseService('Dropdown field must have at least one value');
            }

            $formField = new FormField;
            $formField->key = $validatedData['key']??0;
            $formField->indexno = $validatedData['indexno']??0;
            $formField->title = $validatedData['title'];
            $formField->type_id = $validatedData['type_id'];
            $formField->mandatory = isset( $validatedData['mandatory']) ? 'Y' : 'N';
            $formField->save();

            if (Arr::has($validatedData, 'drop_down_values')) {
                foreach ($validatedData['drop_down_values'] as $value) {
                    $formFieldValue = new FormFieldValue;
                    $formFieldValue->field_id = $formField->id;
                    $formFieldValue->Value = $value['Value'];
                    $formFieldValue->save();
                }
            }
            return (new ApiResponseService())->apiSuccessResponseService($formField);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $formField = FormField::with(['type', 'dropDownValues'])
                ->findOrFail($id);
            return (new ApiResponseService())->apiSuccessResponseService($formField);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //return $request;
        try {
            $validatedData = $request->validate([
                'key' => 'nullable|string|max:255',
                'indexno' => 'nullable|integer',
                'title' => 'required|string|max:255',
                'type_id' => 'required|max:255',
                'mandatory' => 'nullable|string',
                'status' => 'boolean',
                'drop_down_values' => 'nullable|array',
                'drop_down_values.*.Value' => 'nullable|string|max:255',
            ]);

            if ($validatedData['type_id'] == 4 && !Arr::has($validatedData, 'drop_down_values')) {
                return (new ApiResponseService())->apiFailedResponseService('Dropdown field must have at least one value');
            }
        $mandatory=    $validatedData['mandatory']=='Y'? 'Y':'N';
            $formField = FormField::findOrFail($id); // find the form field by ID

            $formField->key = $validatedData['key']??0;
            $formField->indexno = $validatedData['indexno']??0;
            $formField->title = $validatedData['title'];
            $formField->type_id = $validatedData['type_id'];
            $formField->mandatory = $mandatory;
            $formField->status = $validatedData['status'];
            $formField->save();

            if (Arr::has($validatedData, 'drop_down_values')) {

                $formField->dropDownValues()->delete();

                foreach ($validatedData['drop_down_values'] as $value) {
                    $formFieldValue = new FormFieldValue;
                    $formFieldValue->field_id = $formField->id;
                    $formFieldValue->Value = $value['Value'];
                    $formFieldValue->save();
                }
            }
            return (new ApiResponseService())->apiSuccessResponseService($formField);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function getMobileNav()
    {

        try {

            $data = MobileNavBar::get();
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function updateMobileNav(Request $request)
    {

        try {

            $validatedData = $request->validate([
                'key' => 'nullable|string|max:255',
                'status' => 'required|boolean',
                'title' => 'required|string|max:255'
            ]);
            $mobileNav = MobileNavBar::findOrFail($request->id);
            $mobileNav->key = $validatedData['key']??1;
            $mobileNav->title = $validatedData['title'];
            $mobileNav->status = $validatedData['status'];
            $mobileNav->save();
            return (new ApiResponseService())->apiSuccessResponseService($mobileNav);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}