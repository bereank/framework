<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\TaxGroup;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SRI1;

class QUT1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'q_u_t1_s';


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
        return $this->hasMany(SRI1::class, 'ItemCode', 'ItemCode');
    }
    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
    public function ofscs()
    {
        return $this->hasOne(OFSC::class, 'BaseDocEntry');
    }
}
