<?php

namespace Leysco100\Shared\Models\HumanResourse\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\OUDP;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OHEM extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_h_e_m_s';

    public function department()
    {
        return $this->belongsTo(OUDP::class, 'Code', 'dept');
    }

    protected $appends = array('full_name');
    public function getFullNameAttribute()
    {
        return "{$this->firstName} {$this->middleName}  {$this->lastName}";
    }

    public function managr()
    {
        return $this->belongsTo(OHEM::class, 'manager', "empID");
    }

    public function employees()
    {
        return $this->hasMany(OHEM::class, 'manager', "empID");
    }


    public function subordinates()
    {
        $start = microtime(true);
        $data = $this->employees()->with(
            ['subordinates' => function ($query) {
                $query->select('id', 'firstName', 'middleName', 'lastName', 'manager', 'empID');
            }]

        );
        $end = microtime(true);
        $executionTime = ($end - $start);
        Log::info("QUERY Execution time: " . $executionTime . " seconds");
        return $data;
        //   return $this->employees();
    }

    public function peers()
    {
        return $this->hasMany(OHEM::class, "manager", "manager");
    }
    public function empDepartment()
    {
        return $this->hasMany(OHEM::class, "dept", "dept");
    }
    public function empBranch()
    {
        return $this->hasMany(OHEM::class, "branch", "branch");
    }
    public function company()
    {
        return $this->hasMany(OHEM::class, "CompanyID", "CompanyID");
    }
}
