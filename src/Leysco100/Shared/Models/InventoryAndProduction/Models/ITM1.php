<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ITM1 extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'i_t_m1_s';

    protected $appends = array('currency');
    public function OITW()
    {
        return $this->belongsTo(OITW::class);
    }

    public function item()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function opln()
    {
        return $this->belongsTo(OPLN::class, 'PriceList');
    }

    public function getCurrencyAttribute()
    {
        return "KES";
    }
}
