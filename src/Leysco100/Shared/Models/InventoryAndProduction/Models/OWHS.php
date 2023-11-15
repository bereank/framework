<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\BusinessPartner\Models\OBPL;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OWHS extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_w_h_s_s';
    public function whs1()
    {
        return $this->hasMany(WHS1::class, 'WhsCode');
    }

    public function location()
    {
        return $this->belongsTo(OBPL::class, 'BPLId', 'BPLId');
    }

    public function InventoryContents()
    {
        return $this->hasMany(WHS1::class, 'WhsCode');
    }

    public function binlocations()
    {
        return $this->hasMany(OBIN::class, 'WhsCode', 'WhsCode');
    }
}
