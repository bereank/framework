<?php

namespace App\Http\Controllers\API\Administration\Setup\Inventory;

use App\Domains\Administration\Models\OUGP;
use App\Domains\InventoryAndProduction\Models\UGP1;
use App\Domains\Shared\Services\ApiResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\get;

class UoMGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = OUGP::with('ouom')->get();
            foreach ($data as $key => $value) {
                $value->groupDef = UGP1::with('uomentry')->where('UgpEntry', $value->id)->get();
            }
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
            'UgpCode' => 'required',
            'BaseUom' => 'required',
        ]);

        try {
            $uomgroup = OUGP::create([
                'UgpCode' => $request['UgpCode'],
                'UgpName' => $request['UgpName'],
                'BaseUom' => $request['BaseUom'],
            ]);
            //checking if
            $uomgroup = UGP1::create([
                'UgpEntry' => $uomgroup->id,
                'UomEntry' => $request['BaseUom'],
                'AltQty' => 1,
                'BaseQty' => 1,
            ]);

            return (new ApiResponseService())
                ->apiSuccessResponseService();
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }

    public function createugp1(Request $request)
    {
        $this->validate($request, [
            'AltQty' => 'required|numeric',
            'BaseQty' => 'required|numEric',
        ]);

        try {
            $data = UGP1::create([
                'UgpEntry' => $request['UgpEntry'],
                'UomEntry' => $request['UomEntry'],
                'AltQty' => $request['AltQty'],
                'BaseQty' => $request['BaseQty'],
            ]);
            return (new ApiResponseService($data))
                ->apiSuccessResponseService();
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


           // DB::connection()->enableQueryLog();
            $data = OUGP:://with('ouom', 'ugp1.uomentry', 'ugp1.baseuom.ouom')

                 with( 'ouom:id,UomCode,UomName,ExtRef','ugp1:id,UgpEntry,UomEntry,ExtRef','ugp1.uomentry:id,UomCode,UomName,ExtRef')->where('id', $id)
                ->first();


            return (new ApiResponseService())->apiSuccessResponseService($data);
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
        //
    }
}
