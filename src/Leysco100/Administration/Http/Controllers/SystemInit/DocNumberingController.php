<?php

namespace Leysco100\Administration\Http\Controllers\SystemInit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\Administration\Models\NNM1;
use Leysco100\Shared\Models\Administration\Models\NNM2;
use Leysco100\Shared\Models\Administration\Models\ONNM;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Services\ApiResponseService;

class DocNumberingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ONNM::with('objecttype', 'defaultseries')
            ->whereHas('objecttype', function ($q) {
                $q->whereIn('ObjectID', [17, 15, 16, 13, 14, 23, 24, 205, 66, 67, 191, 211, 212, 213, 214]);
            })
            ->orderBy('ObjectCode', 'asc')->get();
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ONNM::with('objecttype', 'series', 'nnm2')->where('id', $id)->first();
        // $apdi = APDI::where('ObjectID',$id)->first();
        // return ONNM::with('objecttype', 'series', 'nnm2')
        //     ->where('ObjectCode', $id)
        //     ->first();
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
        NNM1::where('id', $id)->update([
            'Locked' => $request['Locked'],
            'IsForCncl' => $request['IsForCncl'],
        ]);
    }

    public function updatingSeries(Request $request)
    {
        try {
            foreach ($request['Series'] as $key => $value) {
                $detais = [
                    'Locked' => $value['Locked'],
                    'IsForCncl' => $value['IsForCncl'],
                ];
                NNM1::where('id', $value['id'])->update($detais);
            }

            return response()
                ->json(
                    [
                        'message' => "Updated Successfully",
                    ],
                    200
                );
        } catch (\Throwable $th) {
            return $th;
        }
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

    public function creatingSeries(Request $request)
    {
        $nnm1 = NNM1::firstOrCreate([
            'SeriesName' => $request['SeriesName'], //Series Name
            'ObjectCode' => $request['ObjectCode'], // ID FROM ONNM
        ], [
            'InitialNum' => $request['InitialNum'], //Initial Number
            'NextNumber' => $request['InitialNum'], // NextNumber
            'LastNum' => $request['LastNum'], //LastNum
            'BeginStr' => $request['BeginStr'],
            'EndStr' => $request['EndStr'],
            'Remark' => $request['Remark'],
            'NumSize' => $request['NumSize'],
            'ExtRef' => $request['ExtRef'],
            'Locked' => $request['Locked'] ? $request['Locked'] : "N", //Locked
            'IsForCncl' => $request['IsForCncl'] ? $request['IsForCncl'] : "N", // Is Series for Cancelation
            'GroupCode' => $request['GroupCode'] ? $request['GroupCode'] : 1,
        ]);

        return $nnm1;

        $this->validate($request, [
            'ObjectCode' => 'required',
            'SeriesName' => 'required',
            'InitialNum' => 'required|integer',
        ]);

        $checkIfOverlap = $this->checkIFNumberingOvelap(
            $request['ObjectCode'],
            $request['InitialNum'],
            $request['LastNum'],
            $request['NumSize']
        );

        if ($checkIfOverlap == 0) {
            return (new ApiResponseService())
                ->apiFailedResponseService("Invalid Series Initial Number");
        }
        $nnm1 = NNM1::firstOrCreate([
            'SeriesName' => $request['SeriesName'], //Series Name
            'ObjectCode' => $request['ObjectCode'], // ID FROM ONNM
        ], [
            'InitialNum' => $request['InitialNum'], //Initial Number
            'NextNumber' => $request['InitialNum'], // NextNumber
            'LastNum' => $request['LastNum'], //LastNum
            'BeginStr' => $request['BeginStr'],
            'EndStr' => $request['EndStr'],
            'Remark' => $request['Remark'],
            'NumSize' => $request['NumSize'],
            'ExtRef' => $request['ExtRef'],
            'Locked' => $request['Locked'] ? $request['Locked'] : "N", //Locked
            'IsForCncl' => $request['IsForCncl'] ? $request['IsForCncl'] : "N", // Is Series for Cancelation
            'GroupCode' => $request['GroupCode'] ? $request['GroupCode'] : 1,
        ]);

        return response()
            ->json(
                [
                    'message' => "Creating Series created succesfully",
                ],
                201
            );
    }

    public function setDefaultCurrentUser(Request $request)
    {
        $this->validate($request, [
            'Series' => 'required',
        ]);
        $onnm_id = NNM1::where('id', $request['Series'])->value('ObjectCode');
        $ObjectCode = ONNM::where('id', $onnm_id)->value('ObjectCode');
        if ($ObjectCode) {
            $nnm1 = NNM2::updateOrCreate(
                [
                    'ObjectCode' => $ObjectCode,
                    'UserSign' => Auth::user()->id
                ],
                [ //User
                    'Series' => $request['Series'], //Series
                ]
            );
            return (new ApiResponseService())->apiSuccessResponseService($nnm1);
        }
    }

    public function setDefaultForAllUsers(Request $request)
    {
        $this->validate($request, [
            'Series' => 'required',
        ]);
        $onnm_id = NNM1::where('id', $request['Series'])->value('ObjectCode');
        $ObjectCode = ONNM::where('id', $onnm_id)->value('ObjectCode');
        if ($ObjectCode) {
            $users = User::get();
            foreach ($users as $key => $value) {
                $nnm1 = NNM2::updateOrCreate(
                    [
                        'ObjectCode' => $ObjectCode,
                        'UserSign' => $value->id, //User
                    ],
                    [
                        'Series' => $request['Series'], //Series
                    ]
                );
            }
            return response()
                ->json(
                    [
                        'message' => "Created succesfully",
                    ],
                    201
                );
        }
    }

    public function setDefaultForSelectedUsers(Request $request)
    {
        $this->validate($request, [
            'Series' => 'required',
        ]);

        $onnm_id = NNM1::where('id', $request['Series'])->value('ObjectCode');
        $ObjectCode = ONNM::where('id', $onnm_id)->value('ObjectCode');
        if ($ObjectCode) {
            //NNM2::where('Series', $request['Series'])->where('ObjectCode', $ObjectCode)->delete();
            $users = User::whereIn('id', $request['users'])->pluck('id');
            foreach ($users as $key => $value) {
                $nnm1 = NNM2::updateOrCreate([
                    'ObjectCode' => $ObjectCode,
                    'UserSign' => $value, //User
                ], [
                    'Series' => $request['Series'], //Series
                ]);
            }
            return response()
                ->json(
                    [
                        'message' => "Created succesfully",
                    ],
                    201
                );
        }
    }

    //check if the start is betwwen any value

    public function checkIFNumberingOvelap($ObjectCode, $InitialNum, $LastNum, $NumSize = null)
    {
        if ($NumSize != null) {
            return strlen($NumSize) > $LastNum ? 0 : 1;
        }
        if ($LastNum <= $InitialNum) {
            return $message = "Initial Number and Last Number cannot be equal";
        }
        $nnm1 = NNM1::where('ObjectCode', $ObjectCode)->get();
        foreach ($nnm1 as $key => $value) {
            if (in_array($InitialNum, range($value['InitialNum'], $value['LastNum'])) || in_array($LastNum, range($value['InitialNum'], $value['LastNum']))) {
                return 1;
            }
        }
        return 0;
    }
}
