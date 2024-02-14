<?php

namespace Leysco100\LogisticsHub\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\LogisticsHub\Models\ORPS;
use Leysco100\Shared\Models\Administration\Models\OTER;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Models\LogisticsHub\Models\RoutePlanning;

class ITerritoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $updated_at_gteq = \Request::get('updated_at_gteq');
        try {
            $data = OTER::where(function ($q) use ($updated_at_gteq) {
                if ($updated_at_gteq) {
                    $q->where('updated_at', $updated_at_gteq);
                }
            })
                ->get();
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $parent = null;
            if ($request['parent'] != -1) {
                $parent = OTER::where('ExtRef', $request['parent'])->value('id');
            }
            $data = OTER::create([
                'descript' => $request['descript'],
                'parent' => $parent,
                'ExtRef' => $request['external_unique_key'],
            ]);
            return $data;
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
            $data = OTER::where('id', $id)->get();
            return $data;
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = OTER::findOrFail($id);
        $data->delete();
    }

    public function searchRegion()
    {
        $code = \Request::get('code');
        $external_unique_key = \Request::get('external_unique_key');
        try {
            $data = OTER::where(function ($q) use ($external_unique_key) {
                if ($external_unique_key) {
                    $q->where('ExtRef', $external_unique_key);
                }
            })
                ->get();
            return $data;
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function createOrUpdateRoutes(Request $request)
    {
        $user = Auth::user();

        return ORPS::create([
            'name' => $request['name'],
            'user_id' => $user->id,
            'description' => $request['description'],
        ]);
    }
}
