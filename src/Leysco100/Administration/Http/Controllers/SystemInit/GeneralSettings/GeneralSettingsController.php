<?php

namespace App\Http\Controllers\API\Administration\Setup\SystemInit\GeneralSettings;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthorizationService;
use App\Domains\Administration\Models\OADM;
use App\Domains\Shared\Services\ApiResponseService;
use Leysco\LS100SharedPackage\Models\Domains\Shared\Models\APDI;

class GeneralSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $ObjType = 302;
        // $TargetTables = APDI::with('pdi1')
        //     ->where('ObjectID', $ObjType)
        //     ->first();
        // (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'read');
        $data = OADM::where('id', 1)->first();
        $userDefaults = Auth::user()->oudg;
        $data->DefaultBPLId = $userDefaults ? $userDefaults->BPLId : null; // Default Branch
        return (new ApiResponseService())->apiSuccessResponseService($data);
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
        // return OADM::create([
        //     'MainCurncy' => $request['MainCurncy'], //Main Currency
        //     'DfActCurr' => $request['DfActCurr'], // Default Currency
        //     'SysCurrncy' => $request['SysCurrncy'], // System Currency
        //     'InvntSystm' => $request['InvntSystm'], // Perpetual Inventory System
        //     'DftItmsGrpCod' => $request['DftItmsGrpCod'], // Defautl Item Group
        // ]);
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
        // $ObjType = 302;
        // $TargetTables = APDI::with('pdi1')
        //     ->where('ObjectID', $ObjType)
        //     ->first();
        // (new AuthorizationService())->checkIfAuthorize($TargetTables->id, 'update');
        DB::beginTransaction();
        try {
            $currenlyLoginUser = Auth::user();

            $company = OADM::where('id', 1)->first();
            $oldData = OADM::where('id', 1)->first();

            $generalSettings = [
                'DfltWhs' => $request['DfltWhs'], // Default Warehouse
                'GLMethod' => $request['GLMethod'], // G/L Method
                'DftItmsGrpCod' => $request['DftItmsGrpCod'], // Defautl Item Group
                'copyToUnsyncDocs' => $request['copyToUnsyncDocs'] == true ? 1 : 0,
                'printUnsyncDocs' => $request['printUnsyncDocs'] == true ? 1 : 0,
                'SPEnabled' => $request['SPEnabled'] == true ? 1 : 0,
                'SPAOffline' => $request['SPAOffline'] == true ? 1 : 0,
                'useLocalSearch' => $request['useLocalSearch'] == true ? 1 : 0,
                'NotifAlert' => $request['NotifAlert'],
                'NotifEmail' => $request['NotifEmail'],
                'localUrl' => $request['localUrl'],
            ];

            $company->update($generalSettings);

            //$newData = OADM::where('id', 1)->first();

            // $email = 'bereankibet@gmail.com';
            // Mail::to($email)
            //     ->cc("gilbert.mutai@cargen.com")
            //     ->send(new SetupChangeNotificationMail($oldData, $newData, $currenlyLoginUser->id));

            DB::commit();
            return (new ApiResponseService())->apiSuccessResponseService();
        } catch (\Throwable $th) {
            DB::rollback();
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
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
}
