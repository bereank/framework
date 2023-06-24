<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\TaxGroup;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OITM;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OUOM;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\SRI1;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\ORDR;

class RDR1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'r_d_r1_s';

    public function ordr()
    {
        return $this->belongsTo(ORDR::class, 'DocEntry');
    }

    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function unitofmeasure()
    {
        return $this->belongsTo(OUOM::class, 'UomCode');
    }

    public function sri1()
    {
        return $this->hasMany(SRI1::class, 'ItemCode', 'ItemCode');
    }
    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
}
