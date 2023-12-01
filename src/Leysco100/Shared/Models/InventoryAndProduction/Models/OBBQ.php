<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OBBQ extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];

    public function item()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }


    public function warehouse()
    {
        return $this->belongsTo(OWHS::class, 'WhsCode', 'WhsCode');
    }

    public function bin_location()
    {
        return $this->belongsTo(OBIN::class, 'BinAbs');
    }
}
