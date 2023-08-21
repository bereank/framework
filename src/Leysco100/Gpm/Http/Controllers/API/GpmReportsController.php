<?php

namespace Leysco100\Gpm\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use Leysco100\Gpm\Services\ReportsService;
use Leysco100\Gpm\Http\Controllers\Controller;
use Leysco100\Shared\Services\ApiResponseService;



class  GpmReportsController extends Controller
{
    private function validateRequest(Request $request)
    {
        return $request->validate([
            'FromDate' => 'nullable|date|date_format:Y-m-d|required_with:ToDate',
            'ToDate' => 'nullable|date|date_format:Y-m-d|required_with:FromDate',
            'PerPage' => 'nullable',
        ]);
    }

    public function scanLogReport(Request $request)
    {
        try {
            $validatedData = $this->validateRequest($request);

            $fromDate = array_key_exists('FromDate', $validatedData) ? Carbon::parse($validatedData['FromDate'])->startOfDay() : Carbon::yesterday()->startOfDay();

            $toDate = array_key_exists('ToDate', $validatedData) ? Carbon::parse($validatedData['ToDate'])->endOfDay() : Carbon::yesterday()->endOfDay();

            $perPage = array_key_exists('PerPage', $validatedData) ? $validatedData['PerPage'] : 10;
            $paginate = true;
            $data = (new ReportsService())->scanLogReport($fromDate, $toDate, $paginate, $perPage);

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function duplicateScanLogs(Request $request)
    {
        try {
            $validatedData = $this->validateRequest($request);

            $fromDate = array_key_exists('FromDate', $validatedData) ? Carbon::parse($validatedData['FromDate'])->startOfDay() : Carbon::yesterday()->startOfDay();

            $toDate = array_key_exists('ToDate', $validatedData) ? Carbon::parse($validatedData['ToDate'])->endOfDay() : Carbon::yesterday()->endOfDay();

            $perPage = array_key_exists('PerPage', $validatedData) ? $validatedData['PerPage'] : 10;
            $paginate = true;

            $data = (new ReportsService())->duplicateScanLogsReport($fromDate, $toDate,     $paginate, $perPage);

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function doesNotExistReport(Request $request)
    {
        try {
            $validatedData = $this->validateRequest($request);

            $fromDate = array_key_exists('FromDate', $validatedData) ? Carbon::parse($validatedData['FromDate'])->startOfDay() : Carbon::yesterday()->startOfDay();

            $toDate = array_key_exists('ToDate', $validatedData) ? Carbon::parse($validatedData['ToDate'])->endOfDay() : Carbon::yesterday()->endOfDay();


            $perPage = array_key_exists('PerPage', $validatedData) ? $validatedData['PerPage'] : 10;
            $paginate = true;

            $data = (new ReportsService())->doesNotExistReport($fromDate, $toDate, $paginate, $perPage);

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function documentReport(Request $request)
    {
        try {
            $validatedData = $this->validateRequest($request);

            $fromDate = array_key_exists('FromDate', $validatedData) ? Carbon::parse($validatedData['FromDate'])->startOfDay() : Carbon::yesterday()->startOfDay();

            $toDate = array_key_exists('ToDate', $validatedData) ? Carbon::parse($validatedData['ToDate'])->endOfDay() : Carbon::yesterday()->endOfDay();

            $perPage = array_key_exists('PerPage', $validatedData) ? $validatedData['PerPage'] : 10;
            $paginate = true;
            $data = (new ReportsService())->documentReport($fromDate, $toDate, $paginate, $perPage);

            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
