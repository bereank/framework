<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OIBQ extends Model
{
    use HasFactory, UsesTenantConnection;

    public function bin_location()
    {
        return $this->belongsTo(OBIN::class, 'BinAbs');
    }
}
