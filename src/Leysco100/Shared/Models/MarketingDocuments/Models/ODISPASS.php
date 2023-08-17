<?php

namespace App\Domains\Marketing\Models;

use App\Domains\Administration\Models\ORLP;
use Illuminate\Database\Eloquent\Model;
use App\Domains\Marketing\Models\DISPASS1;
use App\Domains\Administration\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco\LS100SharedPackage\Models\Domains\Shared\Models\APDI;
use Leysco\LS100SharedPackage\Models\Domains\HumanResourse\Models\OHEM;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\OSLP;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\OUDP;
use Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models\OBPL;
use Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models\OCRD;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OLCT;

class ODISPASS extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'o_d_i_s_p_a_s_s';

    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }
    public function rows()
    {
        return $this->hasMany(DISPASS1::class, 'DocEntry');
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
        return $this->belongsTo(ORLP::class, 'SlpCode', 'RlpCode');
    }

    public function ohem()
    {
        return $this->belongsTo(OHEM::class, 'OwnerCode', 'empID');
    }

    public function ocrd()
    {
        return $this->belongsTo(OCRD::class, 'CardCode', 'CardCode');
    }
}
