<?php

namespace Leysco100\Shared\Http\Controllers\API;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\UserFieldsService;
use Leysco100\Shared\Http\Controllers\Controller;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Actions\Helpers\CreateUDFHelperAction;

class UserDefinedFieldsController extends Controller
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ObjType' => 'required',
        ]);

        if ($validator->fails()) {
            return (new ApiResponseService())->apiSuccessAbortProcessResponse($validator->errors());
        }
        try {
            $objType = \Request::get('ObjType');

            $data =  APDI::with('pdi1')->where('ObjectID', $objType)->first();
            $data['doctype'] = $objType;
            if ($data) {
                $user_fields = (new UserFieldsService())->processUDF($data);
            } else {
                return (new ApiResponseService())->apiSuccessAbortProcessResponse([]);
            }

            return (new ApiResponseService())->apiSuccessResponseService($user_fields);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function create(Request $request)
    {


        try {

            $tableName = $request['tableName'];

            $userFields = $request['UserFields'];

            foreach ($userFields as $key =>  $userField) {


                $fieldName =  $userField['FieldName'];
                $fieldDescription =  $userField['FieldDescription'];
                $fieldType =  $userField['FieldType'];
                $fieldSize =  $userField['FieldTypeLength'];

                (new CreateUDFHelperAction($tableName, $fieldName, $fieldDescription, $fieldType, $fieldSize))->handle();
            }




            return (new ApiResponseService())->apiSuccessResponseService();
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
