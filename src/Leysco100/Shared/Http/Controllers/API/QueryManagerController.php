<?php
namespace Leysco100\Shared\Http\Controllers\API;
use Leysco100\Shared\Models\OUQR;
use Leysco100\Shared\Services\ApiResponseService;

class QueryManagerController
{

    public function index()
    {
        try {
            $savedQueries = OUQR::get();
            return (new ApiResponseService())->apiSuccessResponseService($savedQueries);
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $savedQuery = OUQR::find($id);
            return (new ApiResponseService())->apiSuccessResponseService($savedQuery);
        } catch (\Throwable $th) {

            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
