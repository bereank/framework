<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ExOrderItems extends Model
{

    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'ex_order_items';

    public function ordr()
    {
        return $this->belongsTo(ExOrder::class, 'DocEntry');
    }

    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }
}
