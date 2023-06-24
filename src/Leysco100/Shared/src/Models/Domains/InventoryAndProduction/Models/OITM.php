<?php
namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Leysco\LS100SharedPackage\Database\factories\OITMFactory;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\OIDG;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\OUGP;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\TaxGroup;
use Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models\OAT1;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\ITM1;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OITB;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OITW;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OSRN;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OUOM;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\DLN1;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\INV1;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\RDR1;

class OITM extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'o_i_t_m_s';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return OITMFactory::new();
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
        return $this->hasMany(TaxGroup::class, 'code', 'VatGourpSa');
    }
}
