<?php

namespace App\Domains\Marketing\Models;

use App\Domains\Administration\Models\OUDP;
use App\Domains\Administration\Models\User;
use App\Domains\BusinessPartner\Models\OBPL;
use App\Domains\HumanResourse\Models\OHEM;
use App\Domains\InventoryAndProduction\Models\OLCT;
use App\Domains\Shared\Models\APDI;
use Illuminate\Database\Eloquent\Model;

class OPRQ extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_p_r_q_s';

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }

    public function branch()
    {
        return $this->belongsTo(OBPL::class, 'BPLId', 'BPLId');
    }

    public function BusinessPartner()
    {
        return $this->belongsTo(OCRD::class, 'CardCode', 'CardCode');
    }
    public function department()
    {
        return $this->belongsTo(OUDP::class, 'Department');
    }

    public function rows()
    {
        return $this->hasMany(PRQ1::class, 'DocEntry');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }
    public function location()
    {
        return $this->belongsTo(OLCT::class, 'LocationCode');
    }

    public function ohem()
    {
        return $this->belongsTo(OHEM::class, 'OwnerCode', 'empID');
    }
}
