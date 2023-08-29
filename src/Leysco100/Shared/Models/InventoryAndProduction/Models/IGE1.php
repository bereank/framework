<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class IGE1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'i_g_e1_s';
}
