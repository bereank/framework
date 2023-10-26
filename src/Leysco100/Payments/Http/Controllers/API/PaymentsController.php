<?php

namespace Leysco100\Payments\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Models\Payments\Models\OCRP;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Payments\Http\Controllers\Controller;

class PaymentsController extends Controller
{

    public function index(Request $request)
    {

        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 50);
            $data = OCRP::latest()
            ->paginate($perPage, ['*'], 'page', $page);
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function show($id)
    {

        try {
            $data = OCRP::find($id);
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            Log::info($th);
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $data = OCRP::where('id', $id)->update([
                'Balance' => $request['balance'] ?? "",
                'ExtRef' => $request['ExtRef'] ?? "",
                'ExtDocTotal' => $request['ExtDocTotal'] ?? "",
                'ExtRefDocNum' => $request['ExtRefDocNum'] ?? "",
            ]);
            return (new ApiResponseService())->apiSuccessResponseService();
        } catch (\Throwable $th) {
            return response()->json($data);
        }
    }

    
}
