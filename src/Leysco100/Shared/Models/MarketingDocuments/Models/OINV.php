<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\LogisticsHub\Models\OCLG;
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
use Leysco100\Shared\Models\Administration\Models\ORLP;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Leysco100\Shared\Models\Administration\Models\OUDP;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OBPL;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\Administration\Models\Vehicle;
use Leysco100\Shared\Models\MarketingDocuments\Models\OFSC;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OLCT;

class OINV extends Model
{
    use UsesTenantConnection;

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
    public function document_lines()
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

    public function ocrd()
    {
        return $this->belongsTo(OCRD::class, 'CardCode', 'CardCode');
    }

    public function driver()
    {
        return $this->belongsTo(ORLP::class, 'RlpCode', 'RlpCode');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function call()
    {
        return $this->hasOne(OCLG::class, 'id', 'ClgCode');
    }

    public function attachments()
    {
        return $this->hasMany(OATC::class, 'id', 'AtcEntry');
    }
    public function ofscs()
    {
        return $this->hasOne(OFSC::class, 'BaseDocEntry');
    }
}
