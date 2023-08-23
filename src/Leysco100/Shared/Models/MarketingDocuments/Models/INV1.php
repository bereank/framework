<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\TaxGroup;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SRI1;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class INV1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'i_n_v1_s';

    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function sri1()
    {
        return $this->hasMany(SRI1::class, 'LineNum', 'id');
    }
    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
    public function unitofmeasure()
    {
        return $this->belongsTo(OUOM::class, 'UomCode');
    }


    public function ordr()
    {
        return $this->belongsTo(OINV::class, 'DocEntry');
    }
}
