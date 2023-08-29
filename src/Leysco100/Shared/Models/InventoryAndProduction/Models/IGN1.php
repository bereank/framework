<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class IGN1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'i_g_n1_s';

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }
}
