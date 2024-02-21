<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Leysco100\Shared\Models\Administration\Models\OUDP;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OBPL;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OLCT;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ODRF extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_d_r_f_s';

    protected $appends = array('state');
    public function ocrd()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }
    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }

    public function items()
    {
        return $this->hasMany(DRF1::class, 'DocEntry');
    }

    public function rdr1()
    {
        return $this->hasMany(DRF1::class, 'DocEntry');
    }

    public function document_lines()
    {
        return $this->hasMany(DRF1::class, 'DocEntry');
    }

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }

    public function BusinessPartner()
    {
        return $this->belongsTo(OCRD::class, 'CardCode', 'CardCode');
    }

    public function branch()
    {
        return $this->belongsTo(OBPL::class, 'BPLId', 'BPLId');
    }

    public function oslp()
    {
        return $this->belongsTo(OSLP::class, 'SlpCode', 'SlpCode');
    }

    public function department()
    {
        return $this->belongsTo(OUDP::class, 'Department');
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

    public function location()
    {
        return $this->belongsTo(OLCT::class, 'LocationCode');
    }

    public function ohem()
    {
        return $this->belongsTo(OHEM::class, 'OwnerCode', 'empID');
    }
    public function ofscs()
    {
        return $this->hasOne(OFSC::class, 'BaseDocEntry');
    }
}
