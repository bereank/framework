<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Administration\Models\OUDG;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OCLG;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;



class MCallController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $currentTime = Carbon::now();

        $user_id = Auth::user()->id;
       $user_data =   User::where('id', $user_id)->with('oudg')->first();
        $data = OCLG::with('outlet')
            ->with('objectives')
           // ->whereDate('CallDate', '>=', date('Y-m-d'))
            ->where(function ($query) use ($currentTime) {
                $query->where(function ($subQuery) use ($currentTime) {
                    $subQuery->whereDate('CallDate', '=', $currentTime->toDateString())
                        ->whereTime('CallTime', '<=', $currentTime->toTimeString());
                })->orWhere(function ($subQuery) use ($currentTime) {
                    $subQuery->whereDate('CloseDate', '=', $currentTime->toDateString())
                        ->whereTime('CloseTime', '>=', $currentTime->toTimeString());
                });
            })
            ->where(function ($query) use ($user_id, $user_data) {
                $query->orwhere('UserSign', $user_id)
                    ->orwhere('RlpCode', $user_data?->oudg?->Driver);
            })
            ->latest()
            ->get();
        foreach ($data as $value) {
            $value->CallCode = rand(1, 300);
        }

        return $data;
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
        $this->validate($request, [
            'CardCode' => 'required',
            'CallDate' => 'required|date|after_or_equal:today',
            'CallTime' => 'required',
        ]);
        $otherCalls = OCLG::whereDate('CallDate', $request['CallDate'])
            ->where('CardCode', $request['CardCode'])
            ->get();

        if (sizeof($otherCalls)) {
            return response()
                ->json(
                    [
                        'message' => "Another Call is already schedule for that date",
                    ],
                    422
                );
        }

        // try {
            $user = Auth::user();
            $OCLG = OCLG::create([
                'SlpCode' => OUDG::where('id', $user->DfltsGroup)->value('SalePerson'), // Sales Employee
                'CardCode' => $request['CardCode'], // Oulet/Customer
                'CallDate' => $request['CallDate'], //  Call Date
                'CallTime' => $request['CallTime'], // CallTime
                'UserSign'=> $user->id,
                'CallEndTime' => $request['CallEndTime']?? null,// CallTime
                'CloseDate'=> $request['CloseDate']?? null,
                'CloseTime'=>$request['CloseTime'] ?? null,
                'Repeat' => $request['Repeat'] ? $request['Repeat'] : "N", // Recurrence Pattern //A=Annually, D=Daily, M=Monthly, N=None, W=Weekly
            ]);

            return response()
                ->json(
                    [
                        'message' => "Call Created Successfully",
                    ],
                    201
                );
        // } catch (\Throwable $th) {
        //     return response()
        //         ->json(
        //             [
        //                 'message' => $th->getMessage(),
        //             ],
        //             500
        //         );
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = OCLG::with('outlet', 'objectives')
            ->where('id', $id)
            ->get();

        return $data;
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
        $user = OCLG::findOrFail($id);
        // $this->validate($request, [
        //     'Summary' => 'required',
        // ]);

        // $details = [
        //     'Summary' => $request['Summary'],
        // ];

        // OCLG::where('id', $id)->update($details);
        $user->update(array_filter($request->all()));
        return response()
            ->json(
                [
                    'message' => "Updated Successfully",
                ],
                200
            );
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

    public function openCall($id)
    {
        $call = OCLG::where('id', $id)->update([
            "OpenedDate" => now(),
            "Status" => "O",
        ]);

        return response()
            ->json(
                [
                    'message' => "Call Opened Successfully",
                ],
                200
            );
    }

    public function closeCall($id)
    {
        $call = OCLG::where('id', $id)
            ->where('Status', "O")
            ->update([
                "CloseDate" => now(),
                "Status" => "C",
            ]);

        return response()
            ->json(
                [
                    'message' => "Call closed Successfully",
                ],
                200
            );
    }
}