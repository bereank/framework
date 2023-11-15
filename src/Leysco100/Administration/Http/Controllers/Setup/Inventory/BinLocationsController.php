<?php

namespace Leysco100\Administration\Http\Controllers\Setup\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Administration\Http\Controllers\Controller;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBAT;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBFC;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OBSL;


class BinLocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = '';
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    //Bin locations Fields
    public function getBinLocFields()
    {
        try {
            $FieldType = request()->filled('FieldType') ? request()->input('FieldType') : false;
            $data = OBFC::where('id', '!=', NULL);
            if ($FieldType == 'S' || $FieldType == 'A') {
                $data = $data->where('FldType',  $FieldType)->where('Activated', true);
            }
            $data = $data->get();
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function storeBinLocFields(Request $request)
    {
        $Fdata = array_merge($request['SubLevels'], $request['attributes']);

        try {
            foreach ($Fdata  as $data) {
                if (!$data['Activated']) {
                    $obsl = OBSL::where('FldAbs', $data['id'])->first();

                    if ($obsl) {
                        return (new ApiResponseService())
                            ->apiFailedResponseService("Cannot Deactivate Field " . $data['DispName'] . ". Active Records Found");
                    }

                    $obat = OBAT::where('FldAbs', $data['id'])->first();

                    if ($obat) {
                        return (new ApiResponseService())
                            ->apiFailedResponseService("Cannot Deactivate Field " . $data['DispName'] . ". Active Records Found");
                    }
                }


                OBFC::updateorcreate(
                    [
                        'id' => $data['id'],
                    ],
                    [
                        'KeyName' => $data['KeyName'],
                        'Activated' => $data['Activated'],
                        'FldType' => $data['FldType'],
                        'DispName' => $data['DispName'],
                    ]
                );
            }

            return (new ApiResponseService())
                ->apiSuccessResponseService("Updated successfully");
        } catch (\Throwable $th) {
            Log::info($th);
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    //Bin location sublevels
    public function subLevelsIndex()
    {
        try {
            $data = OBSL::with('bin_field')->get();
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function getSubLevel($id)
    {
        try {
            $data = OBSL::find($id);
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function storeSubLevels(Request $request)
    {

        try {
            $this->validate($request, [
                "Descr" => "required",
                "SLCode" => "required",
                "FldAbs" => "required",
            ]);
            $data = OBSL::create([
                "Descr" => $request["Descr"],
                "SLCode" => $request["SLCode"],
                "FldAbs" => $request["FldAbs"],
                "UserSign" => Auth::user()->id,
            ]);
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function editSubLevels(Request $request, $id)
    {
        try {
            $this->validate($request, [
                "Descr" => "required",
                "SLCode" => "required",
                "FldAbs" => "required",
            ]);
            $data = OBSL::where('id', $id)->update([
                "Descr" => $request["Descr"],
                "SLCode" => $request["SLCode"],
                "FldAbs" => $request["FldAbs"],
                "UserSign" => Auth::user()->id,
            ]);
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }


    //Bin location attributes
    public function attributesIndex()
    {
        try {
            $data = OBAT::with('bin_field')->get();
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function getAttribute($id)
    {
        try {
            $data = OBAT::find($id);
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function storeAttributes(Request $request)
    {

        try {
            $this->validate($request, [
                "AttrValue" => "required",
                "FldAbs" => "required",
            ]);
            $data = OBAT::create([
                "AttrValue" => $request["AttrValue"],
                "FldAbs" => $request["FldAbs"],
                "UserSign" => Auth::user()->id,
            ]);
            return (new ApiResponseService())
                ->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    public function editAttributes(Request $request, $id)
    {
        try {
            $this->validate($request, [
                "AttrValue" => "required",
                "FldAbs" => "required",
            ]);
            $data = OBAT::where('id', $id)->update([
                "AttrValue" => $request["AttrValue"],
                "FldAbs" => $request["FldAbs"],
                "UserSign" => Auth::user()->id,
            ]);
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
        $this->validate($request, [
            'UomName' => 'required',
        ]);

        try {
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
    }
}
