<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OSRN extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_s_r_n_s';

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }
}
