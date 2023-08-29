<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;


use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\OUGP;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class UGP1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'u_g_p1_s';

    public function uomentry()
    {
        return $this->belongsTo(OUOM::class, 'UomEntry');
    }

    public function baseuom()
    {
        return $this->belongsTo(OUGP::class, 'UgpEntry');
    }

    public function uom()
    {
        return $this->belongsTo(OUOM::class, 'UomEntry');
    }
}
