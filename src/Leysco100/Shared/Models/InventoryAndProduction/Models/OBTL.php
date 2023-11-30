<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OBTL extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];

    public function ILMMessage()
    {
        return $this->hasMany(OILM::class, 'SL1Abs');
    }

    public function bin_location()
    {
        return $this->belongsTo(OBIN::class, 'BinAbs');
    }

    public function ITLEntry()
    {
        return $this->hasMany(OITL::class, 'ITLEntry');
    }
}
