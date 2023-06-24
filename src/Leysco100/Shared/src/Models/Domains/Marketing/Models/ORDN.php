<?php

namespace Leysco\LS100SharedPackage\Models\Domains\Marketing\Models;

use App\Domains\Shared\Models\APDI;
use Illuminate\Foundation\Auth\User;
use App\Domains\Marketing\Models\RDN1;
use Illuminate\Database\Eloquent\Model;
use App\Domains\HumanResourse\Models\OHEM;
use App\Domains\Administration\Models\OSLP;
use App\Domains\Administration\Models\OUDP;
use App\Domains\BusinessPartner\Models\OBPL;
use App\Domains\BusinessPartner\Models\OCRD;
use App\Domains\InventoryAndProduction\Models\OLCT;

class ORDN extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_r_d_n_s';

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
        return $this->hasMany(RDN1::class, 'DocEntry');
    }

    public function rdn1()
    {
        return $this->hasMany(RDN1::class, 'DocEntry');
    }

    public function rows()
    {
        return $this->hasMany(RDN1::class, 'DocEntry');
    }

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }
    public function department()
    {
        return $this->belongsTo(OUDP::class, 'Department');
    }
    public function BusinessPartner()
    {
        return $this->belongsTo(OCRD::class, 'CardCode', 'CardCode');
    }

    public function location()
    {
        return $this->belongsTo(OLCT::class, 'LocationCode');
    }

    public function branch()
    {
        return $this->belongsTo(OBPL::class, 'BPLId', 'BPLId');
    }

    public function oslp()
    {
        return $this->belongsTo(OSLP::class, 'SlpCode', 'SlpCode');
    }

    public function ohem()
    {
        return $this->belongsTo(OHEM::class, 'OwnerCode', 'empID');
    }
}
