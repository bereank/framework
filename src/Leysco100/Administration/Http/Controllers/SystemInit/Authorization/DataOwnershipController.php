<?php

namespace Leysco100\Administration\Http\Controllers\SystemInit\Authorization;

use Illuminate\Http\Request;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\Administration\Models\DataOwnerships;


class DataOwnershipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $UserID =  request()->filled('UserID') ? request()->input('UserID') : false;
      
        $user = User::with('ohem')->where('id', $UserID)->first();

     
        if ($user->EmpID == null) {
            return (new ApiResponseService())->apiFailedResponseService("User Employee Not Set");
        }

        try {
            $data = APDI::with('ownerships')
                ->with(['ownerships' => function ($query) use ($UserID) {
                    $query->where('UserSign', $UserID);
                }])
                ->select('DocumentName', 'ObjectID')
                ->get();

            foreach ($data as $itm) {
                if ($itm->ownerships == null) {
                    $item = new DataOwnerships();
                    $item->ObjType = $itm->ObjectID;
                    $item->UserSign = $UserID;
                    $item->empId = $user->EmpID;
                    $item->Active = 1;
                    $item->peer =  0;
                    $item->manager =  0;
                    $item->branch = 0;
                    $item->department =  0;
                    $item->team =  0;
                    $item->company =  0;
                    $item->subordinate =  0;
                    $item->save();
                    $itm = $item;
                }
            }
            return (new ApiResponseService())->apiSuccessResponseService($data);
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
            $data = $request->all();
            //  return $data;
            foreach ($data as $item) {
                $dataOwnership =   DataOwnerships::find($item['ownerships']['id']);
                $dataOwnership->fill([
                    'ObjType' => $item['ownerships']['ObjType'],
                    'UserSign' => $item['ownerships']['UserSign'],
                    'Active' => $item['ownerships']['Active'],
                    'peer' => $item['ownerships']['peer'],
                    'manager' => $item['ownerships']['manager'],
                    'branch' => $item['ownerships']['branch'],
                    'department' => $item['ownerships']['department'],
                    'team' => $item['ownerships']['team'],
                    'company' => $item['ownerships']['company'],
                    'subordinate' => $item['ownerships']['subordinate'],
                ]);
                $dataOwnership->save();
            }
            return (new ApiResponseService())->apiSuccessResponseService();
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
        //
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
        //
    }
}
