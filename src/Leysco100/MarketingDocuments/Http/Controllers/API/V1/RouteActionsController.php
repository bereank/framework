<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V1;

use App\Models\OsaPrice;
use Illuminate\Http\Request;
use App\Models\CallObjective;
use App\Models\SosPPlacement;
use Illuminate\Support\Facades\Auth;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;


class RouteActionsController extends Controller
{
    public function OnShelfAvailabilty(Request $request)
    {
        $this->validate($request, [
            'CardCode' => 'required|exists:o_c_r_d_s,id',
            'CallCode' => 'required|exists:o_c_l_g_s,id',
        ]);
        $CardCode = $request['CardCode'];
        $CallCode = $request['CallCode'];
        $Items = $request['Items'];
        $IsItems = is_array($Items) ? 'Yes' : 'No';
        if ($IsItems == "No") {
            return response()
                ->json(
                    [
                        'message' => "Data given is Invalid", 'errors' => [
                            'Items' => 'There  are no Items',
                        ],
                    ],
                    422
                );
        }

        foreach ($Items as $key => $value) {
            $count = $value['Count'];
            if ($count) {
                $osaprice = OsaPrice::updateOrInsert(
                    [
                        'CardCode' => $CardCode,
                        'CallCode' => $CallCode,
                        'ItemCode' => $value['ItemCode'],
                    ],
                    ['Count' => $value['Count']]
                );
            }
        }

        return response()
            ->json(
                [
                    'message' => "Created Successfullt",
                ],
                201
            );
    }

    public function PriceTracking(Request $request)
    {
        $this->validate($request, [
            'CardCode' => 'required|exists:o_c_r_d_s,id',
            'CallCode' => 'required|exists:o_c_l_g_s,id',
        ]);
        $CardCode = $request['CardCode'];
        $CallCode = $request['CallCode'];
        $Items = $request['Items'];
        $IsItems = is_array($Items) ? 'Yes' : 'No';
        if ($IsItems == "No") {
            return response()
                ->json(
                    [
                        'message' => "Data given is Invalid", 'errors' => [
                            'Items' => 'There  are no Items',
                        ],
                    ],
                    422
                );
        }

        foreach ($Items as $key => $value) {
            $count = $value['Price'];
            if ($count) {
                $osaprice = OsaPrice::updateOrInsert(
                    [
                        'CardCode' => $CardCode,
                        'CallCode' => $CallCode,
                        'ItemCode' => $value['ItemCode'],
                    ],
                    ['Price' => $value['Price']]
                );
            }
        }

        return response()
            ->json(
                [
                    'message' => "Created Successfully",
                ],
                201
            );
    }

    public function ShareOfShelf(Request $request)
    {
        $this->validate($request, [
            'CardCode' => 'required|exists:o_c_r_d_s,id',
            'CallCode' => 'required|exists:o_c_l_g_s,id',
        ]);

        $CardCode = $request['CardCode'];
        $CallCode = $request['CallCode'];
        $Items = $request['ProductCategories'];
        $IsItems = is_array($Items) ? 'Yes' : 'No';
        if ($IsItems == "No") {
            return response()
                ->json(
                    [
                        'message' => "Data given is Invalid", 'errors' => [
                            'Items' => 'There  are no Items',
                        ],
                    ],
                    422
                );
        }

        foreach ($Items as $key => $value) {
            $ShelfSize = $value['ShelfSize'];
            $AllotedShelfSize = $value['AllotedShelfSize'];
            if ($AllotedShelfSize && $ShelfSize) {
                $osaprice = SosPPlacement::updateOrInsert(
                    [
                        'CardCode' => $CardCode,
                        'CallCode' => $CallCode,
                        'ItemGrpCode' => $value['ItemGrpCode'],
                    ],
                    [
                        'ShelfSize' => $value['ShelfSize'],
                        'AllotedShelfSize' => $value['AllotedShelfSize'],
                    ]
                );
            }
        }

        return response()
            ->json(
                [
                    'message' => "Created Successfully",
                ],
                201
            );
    }

    public function ProductPlacement(Request $request)
    {
        $this->validate($request, [
            'CardCode' => 'required|exists:o_c_r_d_s,id',
            'CallCode' => 'required|exists:o_c_l_g_s,id',
        ]);

        $CardCode = $request['CardCode'];
        $CallCode = $request['CallCode'];
        $Items = $request['ProductCategories'];
        $IsItems = is_array($Items) ? 'Yes' : 'No';
        if ($IsItems == "No") {
            return response()
                ->json(
                    [
                        'message' => "Data given is Invalid", 'errors' => [
                            'Items' => 'There  are no Items',
                        ],
                    ],
                    422
                );
        }

        foreach ($Items as $key => $value) {
            $PcmtBlocked = $value['PcmtBlocked'];
            $PcmtEyeLevel = $value['PcmtEyeLevel'];
            $PcmtFocusArea = $value['PcmtFocusArea'];
            if ($PcmtBlocked & $PcmtEyeLevel & $PcmtFocusArea) {
                $osaprice = SosPPlacement::updateOrInsert(
                    [
                        'CardCode' => $CardCode,
                        'CallCode' => $CallCode,
                        'ItemGrpCode' => $value['ItemGrpCode'],
                    ],
                    [
                        'PcmtBlocked' => $value['PcmtBlocked'],
                        'PcmtEyeLevel' => $value['PcmtEyeLevel'],
                        'PcmtFocusArea' => $value['PcmtFocusArea']
                    ]
                );
            }
        }
        return response()
            ->json(
                [
                    'message' => "Created Successfully",
                ],
                201
            );
    }

    public function CallObjective(Request $request)
    {
        $this->validate($request, [
            'CallCode' => 'required|exists:o_c_l_g_s,id',
            'Objective' => 'required',
        ]);

        $user = Auth::user();
        $OCLG = CallObjective::create([
            'CallCode' => $request['CallCode'],
            'Objective' => $request['Objective'],
            'UserSign' => $user->id,
        ]);

        return response()
            ->json(
                [
                    'message' => "Created Successfully",
                ],
                201
            );
    }
}
