<?php

namespace Leysco100\Shared\Models\Administration\Models;


use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\UGP1;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OUGP extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_u_g_p_s';

    public function ouom()
    {
        return $this->belongsTo(OUOM::class, 'BaseUom');
    }

    public function ugp1()
    {
        return $this->hasMany(UGP1::class, 'UgpEntry');
    }
}
