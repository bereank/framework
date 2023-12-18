<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;

class SPP2 extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];
    public function uom()
    {
        return $this->belongsTo(OUOM::class, 'UomEntry');
    }

    public function spp3()
    {
        return $this->hasMany(SPP3::class, 'SPP2Num');
    }

    public function fields()
    {
        return $this->hasMany(SPP3::class, 'SPP2Num');
    }

    
}
