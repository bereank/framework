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
}
