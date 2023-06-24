<?php

namespace Leysco100\Gpm\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Leysco\LS100SharedPackage\Services\ApiResponseService;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OLCT;

class LocationController extends Controller
{

    /**
     *
     */

    public function index()
    {
        try {
            $data = OLCT::get();
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
            $data = OLCT::updateOrCreate([
                'Code' => $request['Code'],
            ], [
                'Location' => $request['Location'],
            ]);

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
            $data = OLCT::where('id', $id)->first();
            return (new ApiResponseService())->apiSuccessResponseService($data);
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
            $data = [];
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
}
