<?php

namespace Leysco100\Shared\Services;

/**
 * Service for Marke
 */
class ApiResponseService
{
    /**
     *  Success API Response
     * @return \Illuminate\Http\Response
     */
    public function apiSuccessResponseService($data = null)
    {
        return response()
            ->json([
                'ResultState' => true,
                'ResultCode' => 1200,
                'ResultDesc' => "Operation Was Successful",
                'ResponseData' => $data,
            ], 200);
    }

    public function apiSuccessDraftCreationResponseService($data)
    {
        return response()
            ->json([
                'ResultState' => true,
                'ResultCode' => 1200,
                'ResultDesc' => "Draft Created Successfully",
                'ResponseData' => $data,
            ], 200);
    }

    /**
     * Abort Process Response
     */

    public function apiSuccessAbortProcessResponse($message)
    {
        abort(response()
            ->json([
                'ResultState' => false,
                'ResultCode' => 1500,
                'ValidationError' => "This is validation error",
                'ResultDesc' => $message,
            ], 422));
    }

    public function apiValidationFailedResponse($message)
    {
        abort(response()
            ->json([
                'ResultState' => false,
                'ResultCode' => 1500,
                'ValidationError' => "Validation error",
                'ResultDesc' => $message,
            ], 422));
    }

    /**
     * Not Found Response
     */

    public function apiNotFoundResponse($message)
    {
        abort(response()
            ->json([
                'ResultState' => false,
                'ResultCode' => 1500,
                'ValidationError' => "Not Found",
                'ResultDesc' => $message,
            ], 404));
    }

    /**
     *  Failure API Response
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiFailedResponseService($message, $data = null)
    {
        $response = [
            'ResultState' => false,
            'ResultCode' => 1500,
            'ValidationError' => "Details error",
            'ResultDesc' => $message,
        ];
        if ($data != null) {
            $response["ResultData"] = $data;
        }

        return response()->json($response, 500);
    }

    /**
     *  Failure API Response
     * @return \Illuminate\Http\Response
     */
    public function apiMobileFailedResponseService($message)
    {
        return response()
            ->json([
                'ResultState' => false,
                'ResultCode' => 1500,
                'ResultDesc' => $message,
            ], 500);
    }

    /**
     *  Failure API Response
     * @return \Illuminate\Http\Response
     */
    public function apiIntegratorFailedResponseService(string $message, int $ErrorCode = null, string $ErrorDesc = null)
    {
        return response()
            ->json([
                'ResultState' => false,
                'ResultCode' => 1500,
                'ResultDesc' => $message,
                'ErrorCode' => $ErrorCode,
                'ErrorDesc' => $ErrorDesc,
            ], 200);
    }
}
