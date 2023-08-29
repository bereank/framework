<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OIGE extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_i_g_e_s';

    public function rows()
    {
        return $this->hasMany(IGE1::class, 'DocEntry');
    }

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }
}
