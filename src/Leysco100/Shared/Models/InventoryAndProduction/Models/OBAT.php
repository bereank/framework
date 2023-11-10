<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\User;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OBAT extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_b_a_t';

    public function bin_field()
    {
        return $this->belongsTo(OBFC::class, 'FldAbs');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }
}
