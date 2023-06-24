<?php

namespace App\Domains\InventoryAndProduction\Models;

use App\Domains\Administration\Models\OIDG;
use App\Domains\Administration\Models\OUGP;
use App\Domains\Administration\Models\TaxGroup;
use App\Domains\BusinessPartner\Models\OAT1;
use App\Domains\Marketing\Models\DLN1;
use App\Domains\Marketing\Models\INV1;
use App\Domains\Marketing\Models\RDR1;
use App\Models\ITM15;
use Illuminate\Database\Eloquent\Model;

class OITM extends Model
{
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
