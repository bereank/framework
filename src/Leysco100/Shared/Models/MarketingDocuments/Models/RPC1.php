<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SRI1;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class RPC1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'r_p_c1_s';

    public function orpc()
    {
        return $this->belongsTo(ORPC::class, 'DocEntry');
    }

    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function unitofmeasure()
    {
        return $this->belongsTo(OUOM::class, 'UomCode');
    }

    public function sri1()
    {
        return $this->belongsTo(SRI1::class, 'ItemCode', 'ItemCode');
    }
}
