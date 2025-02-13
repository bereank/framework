<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class SRI1 extends Model
{
    use UsesTenantConnection;

    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 's_r_i1_s';

    public function osrn()
    {
        return $this->belongsTo(OSRN::class, 'SysSerial', 'SysNumber');
    }
}
