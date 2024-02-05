<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;

use Carbon\Carbon;
use App\Models\ExOrder;
use App\Models\ExOrderItems;
use Illuminate\Http\Request;
use Leysco100\Shared\Models\CUFD;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\UserFieldsService;
use Leysco100\Shared\Models\Administration\Models\EOTS;
use Leysco100\MarketingDocuments\Services\SystemDefaults;
use Leysco100\MarketingDocuments\Services\DocumentsService;
use Leysco100\Shared\Models\Administration\Models\TaxGroup;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Leysco100\Shared\Models\MarketingDocuments\Models\ORDR;
use Leysco100\Shared\Models\MarketingDocuments\Models\RDR1;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\UGP1;
use Leysco100\MarketingDocuments\Services\PriceCalculationService;



class MOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $CardCode = \Request::get('CardCode');
        $data = ORDR::with('outlet:id,CardCode,CardName,Address,frozenFor')
            ->with('CreatedBy:id,name')
            ->with(['document_lines' => function ($query) {
                $query->with('ItemDetails:id,ItemCode,ItemName')
                    ->select('id', 'DocEntry', 'Quantity', 'Price', 'LineTotal', 'ItemCode');
            }])
            ->select('id', 'CardCode', 'DocType', 'DocTotal', 'UserSign', 'created_at', 'WddStatus', 'ExtRef', 'ExtRefDocNum')
            ->where(function ($q) {
                $user = Auth::user();
                if ($user->id != 8 || $user->id != 1) {
                    $q->where('UserSign', $user->id);
                }
            })

            ->where(function ($q) use ($CardCode) {
                if ($CardCode) {
                    $q->where('CardCode', $CardCode);
                }
            })
            ->latest()
            ->take(200)
            ->get();

        foreach ($data as $key => $value) {
            $checkErrors = EOTS::where('ObjType', 17)
                ->where('DocEntry', $value->id)
                ->orderBy('id', 'desc')
                ->first();
            //    $value->OrderedItems = $Items;

            $value->WddStatus = "N";
            $value->ErrorMessage = $checkErrors ? $checkErrors->ErrorMessage : "Pending Sync";
            if ($value->ExtRef) {
                $value->WddStatus = "Y";
                $value->ErrorMessage = null;
            }
        }
        return $data;
    }


    public function ExternalOrder(Request $request)
    {
        $user = Auth::user();
        $QuoteDetails = [
            'CardCode' => $request['CardCode'],
            'DocType' => $request['DocType'],
            'DocTotal' => $request['DocTotal'],
            'Comments' => $request['Comment'],
            'ClgCode' => $$request['ClgCode'],
            'UserSign' => $user->id,
        ];
        $newQuote = new ExOrder($QuoteDetails);
        $newQuote->save();
        $OrderID = $newQuote->id;
        if ($OrderID) {
            foreach ($request['Items'] as $key => $value) {
                $Price = $value['UnitPrice'];
                $Pack = $value['Pack'];
                $Quantity = $value['Quantity'];
                $QuoteDetails = [
                    'BaseCard' => $request['CardCode'],
                    'DocEntry' => $OrderID,
                    'ItemCode' => $value['ItemID'],
                    'OrigItem' => $value['ItemID'],
                    'Quantity' => $Quantity,
                    'Price' => $Price,
                    'LineTotal' => $Quantity * $Price,
                    'SlpCode' => $user->id,
                    'UoMEntry' => 1,
                ];
                $OrderItems = new ExOrderItems($QuoteDetails);
                $OrderItems->save();
            }
        }

        return response()
            ->json(
                [
                    'message' => "Secondary Order Created Successfully",
                ],
                201
            );
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

        $this->validate($request, [
            'CardCode' => 'required|exists:tenant.o_c_r_d_s,id',
        ]);

        $Items = $request['Items'];

        if (count($Items) <= 0) {
            return response()
                ->json(
                    [
                        'message' => "Items Required", 'errors' => [
                            'ItemID' => 'Products does not exist',
                        ],
                    ],
                    422
                );
        }

        if ($request['DocTotal'] <= 0) {
            return response()
                ->json(
                    [
                        'message' => "Doc Total Cannot Be Zero", 'errors' => [
                            'DocTotal' => 'Document Total Cannot be Zero',
                        ],
                    ],
                    422
                );
        }
        $TargetTables = APDI::with('pdi1')->where('ObjectID', 17)
            ->first();

        //Getting Document Numner
        $Series = (new DocumentsService())->gettingNumberingSeries($TargetTables->id);
        //Getting Customer Name
        $cardDetails = (new DocumentsService())->getCardName($request['CardCode']);

        //User Defautls
        $systemDefaults = (new SystemDefaults())->getSystemDefaults();

        DB::connection('tenant')->beginTransaction();
        try {
            //Creating Order
            $QuoteDetails = [
                'DocNum' => $Series['DocNum'],
                'CardCode' => $cardDetails->CardCode,
                'CardName' => $cardDetails->CardName,
                'DocType' => "I",
                'DocTotal' => $request['DocTotal'],
                'Comments' => $request['Comment'],
                'NumAtCard' => $request['NumAtCard'] ?? null,
                'SlpCode' => $systemDefaults->SalePerson, // Sales Employee
                'DocDate' => Carbon::now(), //PostingDate
                'TaxDate' => Carbon::now(), //Document Date
                'DocDueDate' => Carbon::now(), // Delivery Date
                'Series' => $Series['Series'],
                'Transfered' => "N",
                'DataSource' => "I",
                'UserSign' => $user->id,
            ];
            $newQuote = new ORDR($QuoteDetails);
            $newQuote->save();
            $OrderID = $newQuote->id;
            if ($OrderID) {
                foreach ($Items as $key => $value) {
                    $product = OITM::where('id', $value['ItemID'])->first();
                    if (!$product) {
                        return response()
                            ->json(
                                [
                                    'message' => "Product does not exist", 'errors' => [
                                        'ItemID' => 'Product does not exist',
                                    ],
                                ],
                                422
                            );
                    }

                    if ($product->frozenFor == "Y") {
                        return response()
                            ->json(
                                [
                                    'message' => "Product is Inactive : " . $product->ItemName, 'errors' => [
                                        'ItemID' => 'Product is Inactive : ' . $product->ItemName,
                                    ],
                                ],
                                422
                            );
                    }
                    $Price = $value['UnitPrice'];

                    if ($Price <= 0) {
                        return response()
                            ->json(
                                [
                                    'message' => "Price cannot be zero", 'errors' => [
                                        'UnitPrice' => 'Price cannot be zero',
                                    ],
                                ],
                                422
                            );
                    }

                    $unitOfMeasure = UGP1::where('id', $value['Pack'])->value('UomEntry');

                    if (!$unitOfMeasure) {
                        return response()
                            ->json(
                                [
                                    'message' => "Unit of Measure is invalid for Item:" . $product->ItemName, 'errors' => [
                                        'Pack' => 'Unit of Measure is invalid For Item: ' . $product->ItemName,
                                    ],
                                ],
                                422
                            );
                    }

                    $priceList = OPLN::where('ListNum', $cardDetails->ListNum)->first();

                    $UomCode = UGP1::where('id', $value['Pack'])->value('UomEntry');
                    $grossPrice = (new PriceCalculationService($product->ItemCode, $priceList->id, $UomCode))->getDefaultPrice();

                    $taxGroup = TaxGroup::where('code', $product->VatGourpSa)->first();
                    $PRICEPERPRICEUNIT = $Price;

                    $Pack = $value['Pack'];
                    $Quantity = $value['Quantity'];

                    $unitPrice = $grossPrice;
                    $vatSum = 0;
                    if ($priceList->isGrossPrc == "Y" && $taxGroup->rate > 0) {
                        $totalRate = $taxGroup->rate + 100;
                        $unitPrice = round($grossPrice * (100 / $totalRate), 2);
                        $vatSum = $Quantity * ($grossPrice - $unitPrice);
                    }

                    $QuoteDetails = [
                        'LineNum' => $key,
                        'BaseCard' => $request['CardCode'],
                        'DocEntry' => $OrderID,
                        'ItemCode' => $product->ItemCode,
                        'Dscription' => $product->ItemName,
                        'OrigItem' => $value['ItemID'],
                        'Quantity' => $Quantity,
                        'OpenInvQty' => $Quantity,
                        'DiscPrcnt' => 0,
                        'Price' => $grossPrice,
                        'PriceBefDi' => $unitPrice,
                        'PriceAfVAT' => $grossPrice,
                        'LineTotal' => $Quantity * $unitPrice,
                        'GTotal' => $Quantity * $grossPrice,
                        'SlpCode' => $systemDefaults->SalePerson,
                        'VatSum' => $vatSum, //    Tax Amount (LC)
                        'UomEntry' => $UomCode,
                        'UomCode' => $UomCode,
                    ];
                    $OrderItems = new RDR1($QuoteDetails);
                    $OrderItems->save();
                }
            }


            (new SystemDefaults())->updateNextNumberNumberingSeries($Series['Series']);

            DB::connection('tenant')->commit();
            return response()
                ->json(
                    [
                        'message' => "Created Successfully",
                    ],
                    201
                );
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollback();
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
        $data = ORDR::with('outlet')->with('CreatedBy')->where('id', $id)->get();

        foreach ($data as $key => $value) {
            $Items = RDR1::select('id', 'Quantity', 'Price', 'LineTotal', 'ItemCode')
                ->with('ItemDetails:id,ItemCode,ItemName')
                ->where('DocEntry', $value->id)
                ->get();
            $value->OrderedItems = $Items;
        }
        return $data;
    }

    public function getOrderTypes()
    {
        try {
            $data = [
                [
                    "id" => 1,
                    "Type" => "Order",
                    "Name" => "Sales Order",
                    "doctype" => 17,
                    "sequence" => 2
                ],
                [
                    "id" => 2,
                    "Type" => "Delivery",
                    "Name" => "Delivery",
                    "doctype" => 15,
                    "sequence" => 3
                ],
                [
                    "id" => 3,
                    "Type" => "Invoice",
                    "Name" => "Invoice",
                    "doctype" => 13,
                    "sequence" => 4
                ],
                [
                    "id" => 4,
                    "Type" => "Payments",
                    "Name" => "Invoice + Payments",
                    "doctype" => 13,
                    "sequence" => 4
                ],
                [
                    "id" => 5,
                    "Type" => "Quotation",
                    "Name" => "Sales Quotation",
                    "doctype" => 23,
                    "sequence" => 1
                ],
                [
                    "id" => 6,
                    "Type" => "ARCreditMemo",
                    "Name" => "A/R Credit Memo",
                    "doctype" => 14,
                    "sequence" => 5
                ],


            ];


            foreach ($data as &$item) {
                $item = (new UserFieldsService())->processUDF($item);
            }

            return $data;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
