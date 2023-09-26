<?php

namespace Leysco100\Shared\Http\Controllers\API;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Leysco100\Shared\Http\Controllers\Controller;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Actions\Helpers\CreateUDFHelperAction;

class UserDefinedFieldsController extends Controller
{

    public function __invoke(Request $request)
    {


        try {

            $tableName = $request['tableName'];

            $userFields = $request['UserFields'];

            foreach ( $userFields as $key =>  $userField) {

              
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
