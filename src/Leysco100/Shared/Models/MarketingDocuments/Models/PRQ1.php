<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\TaxGroup;
use Leysco100\Shared\Models\InventoryAndProduction\Models\SRI1;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class PRQ1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'p_r_q1_s';

    public function sri1()
    {
        return $this->belongsTo(SRI1::class, 'ItemCode', 'ItemCode');
    }

    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
}
