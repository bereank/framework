<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\OIDG;
use Leysco100\Shared\Models\Administration\Models\OUGP;
use Leysco100\Shared\Models\Administration\Models\TaxGroup;
use Leysco100\Shared\Models\BusinessPartner\Models\OAT1;
use Leysco100\Shared\Models\MarketingDocuments\Models\DLN1;
use Leysco100\Shared\Models\MarketingDocuments\Models\INV1;
use Leysco100\Shared\Models\MarketingDocuments\Models\RDR1;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OITM extends Model
{

    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_i_t_m_s';

    protected $appends = array('full_name');
    public function getFullNameAttribute()
    {
        return "{$this->ItemCode} -- {$this->ItemName}";
    }
    public function rdr1()
    {
        return $this->hasMany(RDR1::class, 'ItemCode');
    }

    public function inv1()
    {
        return $this->hasMany(INV1::class, 'ItemCode');
    }

    public function dln1()
    {
        return $this->hasMany(DLN1::class, 'ItemCode');
    }

    public function oat1()
    {
        return $this->hasMany(OAT1::class, 'ItemCode');
    }

    public function itm1()
    {
        return $this->hasOne(ITM1::class, 'ItemCode');
    }
    public function oitw()
    {
        return $this->hasMany(OITW::class, 'ItemCode', 'ItemCode');
    }

    //Inventory UOM Relationship
    public function inventoryuom()
    {
        return $this->belongsTo(OUOM::class, 'InvntryUom');
    }

    //Purchase UOM Relationship
    public function purchaseuom()
    {
        return $this->belongsTo(OUOM::class, 'PUoMEntry');
    }

    //Sales UOM Relationship
    public function salesuom()
    {
        return $this->belongsTo(OUOM::class, 'SUoMEntry');
    }
    public function ougp()
    {
        return $this->belongsTo(OUGP::class, 'UgpEntry');
    }

    //Item Group
    public function oitb()
    {
        return $this->belongsTo(OITB::class, 'ItmsGrpCod');
    }
    //itemproperties

    public function itm15()
    {
        return $this->hasMany(ITM15::class, 'ItemCode');
    }

    public function priceunit()
    {
        return $this->belongsTo(OUOM::class, 'PriceUnit');
    }

    public function oidg()
    {
        return $this->belongsTo(OIDG::class, 'DfltsGroup');
    }

    public function osrn()
    {
        return $this->hasMany(OSRN::class, 'ItemCode', 'ItemCode');
    }
    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'code', 'VatGourpSa');
    }
}
