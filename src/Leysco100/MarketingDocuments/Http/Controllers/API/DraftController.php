<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\LogisticsHub\Models\ODCLG;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\MarketingDocuments\Models\DRF1;
use Leysco100\Shared\Models\MarketingDocuments\Models\ODRF;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;

class DraftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            $searchTerm = $request->input('search') ? $request->input('search') : false;
            $ObjType = $request->input('ObjType') ? $request->input('ObjType') : 17;
            $data = ODRF::with(
                'outlet',
                'document_lines'
                // 'document_lines.oitm.itm1',
                // 'document_lines.oitm.inventoryuom',
                // 'document_lines.oitm.ougp.ouom',
                // 'document_lines.oitm.oitb'
            )->where('ObjType', $ObjType);
          //  $data = ODCLG::where('id', '!=', null);
            if ($searchTerm) {

                $data = $data->where(function ($query) use ($searchTerm) {
                    $query->orWhereDate('created_at', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('id', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('DocNum', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('CardCode', 'LIKE', "%{$searchTerm}%");
                });
            }
            $data = $data->latest()
                ->paginate($perPage, ['*'], 'page', $page);
            return (new ApiResponseService())->apiSuccessResponseService($data);
        } catch (\Throwable $th) {
            return (new ApiResponseService())->apiFailedResponseService($th->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexbyDocObjType(Request $request)
    {

        // return $request;
        $data = ODRF::select('id', 'CardCode', 'DocType', 'DocTotal', 'UserSign', 'created_at', 'ObjType')
            ->where('ObjType', $request->ObjType)->paginate(50);

        foreach ($data as $key => $value) {
            $Items = DRF1::select('id', 'Quantity', 'Price', 'LineTotal', 'ItemCode')
                ->with('ItemDetails:id,ItemCode,ItemName')
                ->where('DocEntry', $value->id)
                ->get();
            $value->Items = $Items;
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
        $user = Auth::user();
        $ORDFDetails = [
            'CardCode' => $request['CardCode'],
            'CardName' => $request['CardName'],
            'SlpCode' => $request['SlpCode'],
            'NumAtCard' => $request['NumAtCard'],
            'CurSource' => $request['CurSource'],
            'DocTotal' => $request['DocTotal'],
            'VatSum' => $request['VatSum'],
            'DocDate' => $request['DocDate'], //PostingDate
            'TaxDate' => $request['TaxDate'], //Document Date
            'DocDueDate' => $request['DocDueDate'], // Delivery Date
            'CntctCode' => $request['CntctCode'], //Contact Person
            'DocNum' => $request['DocNum'], //Number
            'AgrNo' => $request['AgrNo'],
            'LicTradNum' => $request['LicTradNum'],
            'ObjType' => $request['ObjType'],
            'UserSign' => $user->id,
        ];
        $newORDF = new ODRF($ORDFDetails);
        $newORDF->save();

        foreach ($request['document_lines'] as $key => $value) {
            $ORDFRows = [
                'DocEntry' => $newORDF->id,
                'ItemCode' => $value['ItemCode'], //    Item No.
                'CodeBars' => $value['CodeBars'], //    Bar Code
                'SerialNum' => $value['SerialNum'], //    Serial No.
                'Quantity' => $value['Quantity'], //    Quantity
                'DelivrdQty' => $value['DelivrdQty'], //    Delivered Qty
                'InvQty' => $value['InvQty'], //   Qty(Inventory UoM)
                'OpenInvQty' => $value['OpenInvQty'], //Open Inv. Qty ------
                'PackQty' => $value['PackQty'], //    No. of Packages
                'Price' => $value['Price'], //    Price After Discount
                'DiscPrcnt' => $value['DiscPrcnt'], //    Discount %
                'Rate' => $value['Rate'], //    Rate
                'TaxCode' => $value['TaxCode'], //    Tax Code
                'PriceAfVAT' => $value['PriceAfVAT'], //       Gross Price after Discount
                'PriceBefDi' => $value['PriceBefDi'], // Unit Price
                'LineTotal' => $value['LineTotal'], //    Total (LC)
                'WhsCode' => $value['WhsCode'], //    Warehouse Code
                'ShipDate' => $value['ShipDate'], //    Del. Date
                'SlpCode' => $value['SlpCode'], //    Sales Employee
                'Commission' => $value['Commission'], //    Comm. %
                'AcctCode' => $value['AcctCode'], //    G/L Account
                'OcrCode' => $value['OcrCode'], //    Dimension 1
                'OcrCode2' => $value['OcrCode2'], //    Dimension 2
                'OcrCode3' => $value['OcrCode3'], //    Dimension 3
                'OcrCode4' => $value['OcrCode4'], //    Dimension 4
                'OcrCode5' => $value['OcrCode5'], //    Dimension 5
                'OpenQty' => $value['OpenQty'], //    Open Inv. Qty
                'GrossBuyPr' => $value['GrossBuyPr'], //   Gross Profit Base Price
                'GPTtlBasPr' => $value['GPTtlBasPr'], //    Gross Profit Total Base Price
                'TreeType' => $value['TreeType'], //    BOM Type
                'TargetType' => $value['TargetType'], //    Target Type
                'BaseType' => $value['BaseType'], //    Base Type
                'BaseRef' => $value['BaseRef'], //    Base Ref.
                'BaseEntry' => $value['BaseEntry'], //    Base Key
                'BaseLine' => $value['BaseLine'], //    Base Row
                'SpecPrice' => $value['SpecPrice'], //    Price Source
                'VatSum' => $value['VatSum'], //    Tax Amount (LC)
                'GrssProfit' => $value['GrssProfit'], //    Gross Profit (LC)
                'PoTrgNum' => $value['PoTrgNum'], //    Procurement Doc.
                'OrigItem' => $value['OrigItem'], //    Original Item
                'BackOrdr' => $value['BackOrdr'], //    Partial Delivery
                'FreeTxt' => $value['FreeTxt'], //    Free Text
                'TrnsCode' => $value['TrnsCode'], //    Shipping Type
                'UomCode' => $value['UomCode'], //    UoM Code
                'unitMsr' => $value['unitMsr'], //    UoM Name
                'NumPerMsr' => $value['NumPerMsr'], //    Items per Unit
                'Text' => $value['Text'], //    Item Details
                'OwnerCode' => $value['OwnerCode'], //    Owner
                'GTotal' => $value['GTotal'], //    Gross Total
                'AgrNo' => $value['AgrNo'], //    Blanket Agreement No.
                'LinePoPrss' => $value['LinePoPrss'], //    Allow Procmnt. Doc.
            ];
            $drf1 = new DRF1($ORDFRows);
            $drf1->save();
        }

        return response()
            ->json(
                [
                    'Message' => "Created Successfully",
                ],
                201
            );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ODRF::with(
            'outlet',
            'inv1.oitm.itm1',
            'inv1.oitm.inventoryuom',
            'inv1.oitm.ougp.ouom',
            'inv1.oitm.ougp.ugp1',
            'inv1.oitm.oitb'
        )
            ->where('id', $id)
            ->first();
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
