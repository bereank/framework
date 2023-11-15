<?php

namespace Leysco100\Shared\Services;

use Gate;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
use Leysco100\Shared\Models\Administration\Models\OUDG;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OBPL;
use Leysco100\Shared\Models\Administration\Models\Permission;
use Leysco100\Shared\Models\Administration\Models\DataOwnerships;


/**
 * Service for Marke
 */
class AuthorizationService
{
    /**
     *  Authorization Response
     */
    public function checkIfAuthorize($ObjType, $Action)
    {

        $Permission = Permission::where('apdi_id', $ObjType)
            ->where('Label', $Action)
            ->pluck('name');
        abort_if(
            Gate::denies($Permission),
            response()
                ->json(
                    [
                        'ResultState' => true,
                        'ResultCode' => 1043,
                        'ResultDesc' => "Not Authorized",
                    ],
                    200
                )
        );
    }

    public function checkIfSMSisEnable()
    {
        return 1;
    }

    /**
     * Mobile Menu
     */

    public function mobileNavBar($userID = null)
    {

        $userID = $userID ?? Auth::user()->id;
        $user = User::where('id', $userID)->first();
        $nav_array = [
            [
                "title" => "Home",
                "key" => "home",
            ],
            [
                "title" => "Targets",
                "key" => "sales-targets",

            ],
            [
                "title" => "Customers",
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
            [
                "title" => "Sales",
                "key" => "sales",
            ],
            [
                "title" => "Dispatch",
                "key" => "assigned-delivery",
            ],
            [
                "title" => "Inventory",
                "key" => "inventory",
            ],
        ];
        if ($user && $user->SUPERUSER) {
            $nav_array[] = [

                "title" => "Dispatch",
                "key" => "clerk",

            ];
        }
        $nav_array[] =
            [
                "title" => "Settings",
                "key" => "setting",
            ];

        if ($user && $user->SUPERUSER) {
            $nav_array[] = [
                "title" => "Gps",
                "key" => "gps",
            ];
        }
        return  $nav_array;
    }

    public function getCurrentLoginUserBranches($userID = null)
    {
        $userID = $userID ?? Auth::user()->id;

        $user = User::where('id', $userID)->first();

        if ($user->SUPERUSER == 1) {
            return OBPL::orderBy('LocationCode')->get();
        }
        $userDefaulf = OUDG::where('id', $user->DfltsGroup)->first();

        $branches = $user->branches;
        if ($userDefaulf->BPLId) {
            $branch = OBPL::where('id', $userDefaulf->BPLId)->first();
            $exists = $user->branches->contains($userDefaulf->BPLId);
            if (!$exists && $branch) {
                $branches->push($branch);
            }
        }

        return $branches;
    }

    public function getDefaultCurrentLoginUser()
    {
        $user = Auth::user();
        $userDefaulf = OUDG::where('id', $user->DfltsGroup)->first();

        $branches = $user->branches;
        if ($userDefaulf->BPLId) {
            $branch = OBPL::where('id', $userDefaulf->BPLId)->first();
            $exists = $user->branches->contains($userDefaulf->BPLId);
            if (!$exists && $branch) {
                $branches->push($branch);
            }
        }

        return $branches;
    }

    public function getDataOwnershipAuth($ObjType, $operation)
    {
        $empID = Auth::user()->EmpID;
        $manager = OHEM::where('empID', $empID)->select('id', 'firstName', 'middleName', 'lastName', 'manager', 'empID', 'dept', 'branch', 'CompanyID')
            ->with(['managr' => function ($query) {
                $query->select('id', 'firstName', 'middleName', 'lastName', 'manager', 'empID', 'dept', 'branch', 'CompanyID');
            }])
            ->with(['employees' => function ($query) {
                $query->select('id', 'firstName', 'middleName', 'lastName', 'manager', 'empID', 'dept', 'branch', 'CompanyID');
            }])
            ->with(['subordinates' => function ($query) {
                $query->select('id', 'firstName', 'middleName', 'lastName', 'manager', 'empID', 'dept', 'branch', 'CompanyID');
            }])
            ->with(['peers' => function ($query) {
                $query->select('id', 'firstName', 'middleName', 'lastName', 'manager', 'empID', 'dept', 'branch', 'CompanyID');
            }])
            ->with(['empDepartment' => function ($query) {
                $query->select('id', 'firstName', 'middleName', 'lastName', 'manager', 'empID', 'dept', 'branch', 'CompanyID');
            }])
            ->with(['empBranch' => function ($query) {
                $query->select('id', 'firstName', 'middleName', 'lastName', 'manager', 'empID', 'dept', 'branch', 'CompanyID');
            }])
            ->with(['company' => function ($query) {
                $query->select('id', 'firstName', 'middleName', 'lastName', 'manager', 'empID', 'dept', 'branch', 'CompanyID');
            }])
            ->first();


        $udata = APDI::with('ownerships')
            ->with(['ownerships' => function ($query) use ($empID) {
                $query->where('empID', $empID);
            }])
            ->where('ObjectID', $ObjType)
            ->select('DocumentName', 'ObjectID')
            ->first();

        $allowed = collect();
        $udata = $udata->ownerships;
        $allowed->push($empID);
        $operator = $operation == 1 ? '||' : '&&';

        $expression = "\$udata->peer == 1 $operator \$udata->peer == 2";

        if (eval("return ($expression);")) {
            $peers = $manager->peers?->pluck('empID');
            $allowed->push($peers);
        }
        $expression = "\$udata->branch == $operation $operator \$udata->branch == 2";
        if (eval("return ($expression);")) {
            $empBranch = $manager->empBranch?->pluck('empID');
            $allowed->push($empBranch);
        }
        $expression = "\$udata->dept == $operation $operator \$udata->dept == 2";
        if (eval("return ($expression);")) {
            $empDepartment = $manager->empDepartment?->pluck('empID');
            $allowed->push($empDepartment);
        }
        $expression = "\$udata->company == $operation $operator \$udata->company == 2";
        if (eval("return ($expression);")) {
            Log::info(["Company9", true]);
            $company = $manager->company?->pluck('empID');
            $allowed->push($company);
            Log::info(["Company", true]);
        }
        $expression = "\$udata->manager == $operation $operator \$udata->manager == 2";
        if (eval("return ($expression);")) {
            $managr = $manager->managr?->pluck('empID');
            $allowed->push($managr);
        }
        $expression = "\$udata->subordinate == $operation $operator \$udata->subordinate == 2";
        if (eval("return ($expression);")) {
            $data = $manager->subordinates;
            $uniqueEmpIds = [];
            $sub = $this->getAllUniqueEmpIds($data, $uniqueEmpIds);
            $allowed->push($sub);
        }


        $allowed = Arr::flatten([$allowed]);

        $filteredArray = collect($allowed)->filter(function ($item) {
            return $item !== null;
        })->values();
        $resultArray = $filteredArray->unique()->values()->all();
        return  $resultArray;
    }
    public function CheckAllowedEdit($ObjType, $empID)
    {
        $data =   $this->getDataOwnershipAuth($ObjType, 2);
        if (in_array($empID, $data)) {
            return true;
        } else {
            abort_if(
                Gate::denies(false),
                response()
                    ->json(
                        [
                            'ResultState' => true,
                            'ResultCode' => 1043,
                            'ResultDesc' => "Not Authorized",
                        ],
                        200
                    )
            );
        }
    }
    public function CheckIfActive($ObjType, $empID)
    {
        $data = DataOwnerships::where('ObjType', $ObjType)->where('EmpId', $empID)
            ->where('Active', 1)
            ->select('Active')->first();
        return $data;
    }
    public  function getAllUniqueEmpIds($data, &$empIds)
    {
        foreach ($data as $item) {
            if (isset($item['empID']) && $item['empID'] !== null) {
                $empIds[] = $item['empID'];
            }
            if (isset($item['subordinates']) && !empty($item['subordinates'])) {
                $this->getAllUniqueEmpIds($item['subordinates'], $empIds);
            }
        }
        return  $empIds;
    }
}