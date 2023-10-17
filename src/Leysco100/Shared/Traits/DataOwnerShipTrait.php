<?php

namespace Leysco100\Shared\Traits;

use Gate;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
use Leysco100\Shared\Models\Administration\Models\DataOwnerships;

trait DataOwnerShipTrait
{
   public static function bootOwnerable()
    {
        if (auth()->check()) {  
            static::addGlobalScope('tenant_id', function (Builder $builder) {
                if (Auth::user()->type == "super admin") {
                    return;
                } else {
                    return $builder->where('tenant_id', Auth::user()->tenant_id);
                }
            });
        }
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
            // Log::info(["Company9", true]);
            $company = $manager->company?->pluck('empID');
            $allowed->push($company);
            // Log::info(["Company", true]);
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
