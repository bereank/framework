<?php

namespace Leysco100\LogisticsHub\Services;

use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\LogisticsHub\Models\OCLG;
use Leysco100\Shared\Models\Administration\Models\OUDG;


/**
 * Inventory Services
 */
class RouteMgtService
{
    /**
     *  Creating nne call
     * @param array $CallsData
     */
    public function CreateCallsService($CallsData)
    {
        $user = Auth::user();
        $OCLG = OCLG::create([
            'ClgCode' => $CallsData['ClgCode'] ?? null,
            'SlpCode' => $CallsData['SlpCode'] ?? OUDG::where('id', $user->DfltsGroup)->value('SalePerson'), // Sales Employee
            'CardCode' => $CallsData['CardCode'] ?? null, // Oulet/Customer
            'CallDate' => $CallsData['CallDate'] ?? null, //  Call Date
            'CallTime' => $CallsData['CallTime'] ?? null, // CallTime
            'UserSign' => $user->id,
            'RouteCode' => $CallsData['RouteCode'] ?? null,
            'CallEndTime' => $CallsData['CallEndTime'] ?? null, // CallTime
            'CloseDate' => $CallsData['CloseDate'] ?? null,
            'CloseTime' => $CallsData['CloseTime'] ?? null,
            'Repeat' => $CallsData['Repeat'] ? $CallsData['Repeat'] : "N", // Recurrence Pattern //A=Annually, D=Daily, M=Monthly, N=None, W=Weekly
            'Summary' => $CallsData['Summary'] ?? null,
            'Status' => $CallsData['Status'] ?? 'D',
        ]);
        return $OCLG;
    }
}
