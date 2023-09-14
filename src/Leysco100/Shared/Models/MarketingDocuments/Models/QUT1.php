<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class QUT1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'q_u_t1_s';

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }
}
