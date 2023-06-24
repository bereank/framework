<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Marketing\Models;

use App\Domains\Administration\Models\OSLP;
use App\Domains\Administration\Models\OUDP;
use App\Domains\BusinessPartner\Models\OBPL;
use App\Domains\BusinessPartner\Models\OCRD;
use App\Domains\HumanResourse\Models\OHEM;
use App\Domains\InventoryAndProduction\Models\OLCT;
use App\Domains\Marketing\Models\INV1;
use App\Domains\Shared\Models\APDI;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class OINV extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_i_n_v_s';

    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }

    public function inv1()
    {
        return $this->hasMany(INV1::class, 'DocEntry');
    }
    public function rows()
    {
        return $this->hasMany(INV1::class, 'DocEntry');
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
