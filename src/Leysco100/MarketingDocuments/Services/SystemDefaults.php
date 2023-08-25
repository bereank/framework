<?php

namespace Leysco100\MarketingDocuments\Services;

use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\Administration\Models\NNM1;
use Leysco100\Shared\Models\Administration\Models\ONNM;
use Leysco100\Shared\Models\Administration\Models\OUDG;

/**
 * System Defaults
 */
class SystemDefaults
{
    /**
     *  Getting user Defaults
     */
    public function getSystemDefaults()
    {
        $user = Auth::user();
        $userDefaults = OUDG::where('id', $user->DfltsGroup)->first();

        return $userDefaults;
    }

    public function getDftNumberingSeries($ObjType)
    {
        $form = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        $DfltSeries = ONNM::where('ObjectCode', $form->id)
            ->value('DfltSeries');
        $documentDefaultSeries = NNM1::where('id', $DfltSeries)
            ->where('Locked', 'N')
            ->first();

        $documentDefaultSeries->NextNumber =
            $documentDefaultSeries->BeginStr . sprintf("%0" . $documentDefaultSeries->NumSize . "s", $documentDefaultSeries->NextNumber) . $documentDefaultSeries->EndStr;

        return $documentDefaultSeries;
    }

    public function updateNextNumberNumberingSeries($series)
    {
        $NextNumber = NNM1::where('id', $series)->value('NextNumber') + 1;
        $nnm1 = NNM1::where('id', $series)->update(['NextNumber' => $NextNumber]);

        return "Updated";
    }
}
