<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class WTR1 extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'w_t_r1_s';

    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function sri1()
    {
        return $this->hasMany(SRI1::class, 'ItemCode', 'ItemCode');
    }
}
