<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\TaxGroup;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SRI1;

class RDN1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'r_d_n1_s';

    public function ordn()
    {
        return $this->belongsTo(ORDN::class, 'DocEntry');
    }

    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
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
        return $this->hasMany(SRI1::class, 'LineNum', 'id');
    }

    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
}
