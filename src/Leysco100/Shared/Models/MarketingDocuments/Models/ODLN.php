<?php

namespace App\Domains\Marketing\Models;

use App\Domains\Shared\Models\APDI;
use Illuminate\Database\Eloquent\Model;
use App\Domains\HumanResourse\Models\OHEM;
use App\Domains\Administration\Models\OSLP;
use App\Domains\Administration\Models\OUDP;
use App\Domains\Administration\Models\User;
use App\Domains\BusinessPartner\Models\OBPL;
use App\Domains\BusinessPartner\Models\OCRD;
use App\Domains\InventoryAndProduction\Models\OLCT;

class ODLN extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_d_l_n_s';

    protected $appends = array('state');
    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }

    public function dln1()
    {
        return $this->hasMany(DLN1::class, 'DocEntry');
    }

    public function rows()
    {
        return $this->hasMany(DLN1::class, 'DocEntry');
    }

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }

    public function BusinessPartner()
    {
        return $this->belongsTo(OCRD::class, 'CardCode', 'CardCode');
    }
    public function department()
    {
        return $this->belongsTo(OUDP::class, 'Department');
    }

    public function branch()
    {
        return $this->belongsTo(OBPL::class, 'BPLId', 'BPLId');
    }

    public function oslp()
    {
        return $this->belongsTo(OSLP::class, 'SlpCode', 'SlpCode');
    }
    public function location()
    {
        return $this->belongsTo(OLCT::class, 'LocationCode');
    }

    public function getStateAttribute()
    {
        if ($this->status == "O") {
            return "Open";
        }

        if ($this->status == "C") {
            return "Closed";
        }
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }

    public function ohem()
    {
        return $this->belongsTo(OHEM::class, 'OwnerCode', 'empID');
    }
}
