<?php

namespace Leysco100\Shared\Models\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\BusinessPartner\Models\CQG1;
use Leysco100\Shared\Models\BusinessPartner\Models\Employee;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OCQG extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_c_q_g_s';

    public function cqg1()
    {
        return $this->hasMany(CQG1::class, 'GroupCode');
    }

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'SlpCode');
    }
}
