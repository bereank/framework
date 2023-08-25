<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ITM15 extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'i_t_m15_s';

    public function itg1()
    {
        return $this->belongsTo(ITG1::class, 'QryGroup');
    }

    public function oitg()
    {
        return $this->belongsTo(OITG::class, 'ItmsTypCod');
    }
}
