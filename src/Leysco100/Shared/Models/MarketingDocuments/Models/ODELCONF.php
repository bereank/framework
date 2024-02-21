<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\LogisticsHub\Models\OCLG;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
use Leysco100\Shared\Models\Administration\Models\ORLP;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Leysco100\Shared\Models\Administration\Models\OUDP;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OBPL;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\Administration\Models\Vehicle;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OLCT;
use Leysco100\Shared\Models\MarketingDocuments\Models\DELCONF1;

class ODELCONF extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_d_e_l_c_o_n_f';

    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }
    public function document_lines()
    {
        return $this->hasMany(DELCONF1::class, 'DocEntry');
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
    public function driver()
    {
        return $this->belongsTo(ORLP::class, 'RlpCode', 'RlpCode');
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    public function ohem()
    {
        return $this->belongsTo(OHEM::class, 'OwnerCode', 'empID');
    }

    public function ocrd()
    {
        return $this->belongsTo(OCRD::class, 'CardCode', 'CardCode');
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
