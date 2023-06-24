<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\OSLP;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\OUDP;
use Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models\OBPL;
use Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models\OCRD;
use Leysco\LS100SharedPackage\Models\Domains\HumanResourse\Models\OHEM;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OLCT;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\DRF1;
use Leysco\LS100SharedPackage\Models\Domains\Shared\Models\APDI;

class ODRF extends Model
{
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

    public function rows()
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
}
