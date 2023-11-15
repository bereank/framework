<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OBIN extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_b_i_n';

    public function sublevel()
    {
        return $this->hasMany(OBSL::class, 'SL1Abs');
    }

    public function warehouse()
    {
        return $this->belongsTo(OWHS::class, 'WhsCode', 'WhsCode');
    }

    public function bin_items()
    {
        return $this->hasMany(OIBQ::class, 'BinAbs');
    }
}
