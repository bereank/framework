<?php

namespace App\Domains\ServiceCall\Services;

use App\Domains\Shared\Services\ApiResponseService;

/**
 * Purchase and Marketing Document Validation Service
 */
class GeneralServiceCallValidationSerivce
{
    /**
     *  Entry Point for Document Validation
     *
     * @param  \Illuminate\Http\Request  $request
     */

    public function documentHeaderValidation($request)
    {
        if (!$request['customer']) {
            (new ApiResponseService())->apiSuccessAbortProcessResponse("Customer Required");
        }

        if (!$request['subject']) {
            (new ApiResponseService())->apiSuccessAbortProcessResponse("Service Call subject is required");
        }
    }
}
