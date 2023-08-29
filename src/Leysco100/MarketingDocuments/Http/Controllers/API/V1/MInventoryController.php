<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\MarketingDocuments\Jobs\NumberingSeries;
use Leysco100\MarketingDocuments\Services\DocumentsService;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITB;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OWHS;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OWTQ;
use Leysco100\Shared\Models\InventoryAndProduction\Models\WTQ1;



class MInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OWTQ::with('document_lines')->get();
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
        DB::beginTransaction();
        try {
            $TargetTables = APDI::with('pdi1')->where('ObjectID', 66)
                ->first();

            //Getting Document Numner
            $Series = (new DocumentsService())->gettingNumberingSeries($TargetTables->id);

            $user = Auth::user();

            $NewDocDetails = [
                'ObjType' => 66,
                'DocNum' => $Series['DocNum'],
                'ToWhsCode' => 2,
                'DocDate' => Carbon::now(), //PostingDate
                'TaxDate' => Carbon::now(), //Document Date
                'DocDueDate' => Carbon::now(), // Delivery Date
                'UserSign' => $user->id,
            ];
            $newDoc = new $TargetTables->ObjectHeaderTable($NewDocDetails);
            $newDoc->save();

            foreach ($request['Items'] as $key => $value) {
                $rowdetails = [
                    'DocEntry' => $newDoc->id,
                    'LineNum' => $key + 1, //    Row Number
                    'FromWhsCod' => $request['FromWhsCod'],
                    'ItemCode' => $value['ItemCode'], //    Item No.
                    'Dscription' => $value['Dscription'], // Item Description
                    'Quantity' => $value['Quantity'], //    Quantity
                    'UomEntry' => $value['UomEntry'],
                    'Price' => $value['Price'], //    Price After Discount
                ];
                $rowItems = new WTQ1($rowdetails);
                $rowItems->save();
            }

            //Updating the NextNumber
            NumberingSeries::dispatchSync($Series['Series']);
            DB::commit();
            return response()
                ->json(
                    [
                        "RequestID" => $newDoc->id,
                        'message' => "Created Successfully",
                    ],
                    201
                );
        } catch (\Throwable $th) {
            DB::rollback();
            return response()
                ->json(
                    [

                        'message' => $th->getMessage(),
                    ],
                    500
                );
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

    public function getMyStock()
    {
        $data = OITM::select('id', 'ItemName', 'ItemCode', 'OnHand')
            ->orderBy('ItemName', 'asc')
            ->where('frozenFor', "N")
            ->paginate(200);
        return $data;
    }

    public function getWarehouse()
    {
        return OWHS::get();
    }

    public function getProductCategory()
    {
        return OITB::select('id', 'ItmsGrpNam')->get();
    }
}
