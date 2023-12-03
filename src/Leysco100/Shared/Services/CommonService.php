<?php
namespace Leysco100\Shared\Services;

use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\FormSetting\Models\FM100;
use Leysco100\Shared\Models\Administration\Models\NNM1;
use Leysco100\Shared\Models\Administration\Models\NNM2;
use Leysco100\Shared\Models\Administration\Models\ONNM;

/**
 * Common Item Services
 */
class CommonService
{

    /**
     * Get Single Document Details
     * @param Int $ObjType
     * @param Int $DocEntry => Document Auto Increment ID
     */
    public function getSingleDocumentDetails(int $ObjType, int $DocEntry)
    {
        $DocumentTables = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();
        $document = $DocumentTables->ObjectHeaderTable::with('objecttype', 'document_lines')
            ->where('id', $DocEntry)
            ->first();
        return $document;
    }

    /**
     *Getting Document Numbering Series Document

     */
    public function gettingObjectNumberingSeries(int $ObjectType, int $UserSign = null)
    {

        $ObjectCode = APDI::with('pdi1')->where('ObjectID', $ObjectType)
            ->value('id');

        $UserSign = $UserSign ?? Auth::user()->id;
        //default Numembering Seires
        $documentDefaultSeries = NNM1::where('id', ONNM::where('ObjectCode', $ObjectCode)
                ->value('DfltSeries'))
            ->where('Locked', 'N')
            ->first();

        $currentUserDefaultSeries = NNM2::with('nnm1')->where('ObjectCode', $ObjectCode)
            ->whereHas('nnm1', function ($q) {
                $q->where('Locked', 'N');
            })
            ->where('UserSign', $UserSign)
            ->first();

        //
        if ($currentUserDefaultSeries) {
            $nnm1Data = NNM1::where('id', $currentUserDefaultSeries['Series'])
                ->where('Locked', 'N')
                ->first();
            $nnm1Data->NextNumber = sprintf("%0" . $nnm1Data->NumSize . "s", $nnm1Data->NextNumber);

            $documentDefaultSeries = $nnm1Data;
        }

        $DocNum = sprintf("%0" . $documentDefaultSeries->NumSize . "s", $documentDefaultSeries->NextNumber);

        $details = [
            'DocNum' => $DocNum,
            'Series' => $documentDefaultSeries->id,
        ];

        return $details;
    }

    /**
     * Mobile Menu
     */

    public function mobileNavBar()
    {

        return [
            [
                "title" => "Home",
                "key" => "home",
            ],
            [
                "title" => "Outlets",
                "key" => "outlet",
            ],
            [
                "title" => "Calls",
                "key" => "call",
            ],
            [
                "title" => "Orders",
                "key" => "order",
            ],
            // [
            //     "title" => "Assigned Delivery",
            //     "key" => "assigned-delivery",
            // ],
            [
                "title" => "Inventory",
                "key" => "inventory",
            ],
            [
                "title" => "Settings",
                "key" => "setting",
            ],
        ];
    }
    public function createOrUpdateMenu($menuData, $parentID = null, $UserSign = 1)
    {
        foreach ($menuData as $item) {
            $menu = FM100::updateOrCreate([
                'UserSign' => $UserSign,
                'ParentID' => $parentID,
                'Label' => $item['Label'],
                'Visible' => $item['Visible'],
                'icon' => array_key_exists('icon', $item) ? $item['icon'] : null,
                'link' => array_key_exists('link', $item) ? $item['link'] : null,
            ]);
            if (isset($item['children']) && is_array($item['children'])) {
                $this->createOrUpdateMenu($item['children'], $menu->id);
            }
        }
    }
}
