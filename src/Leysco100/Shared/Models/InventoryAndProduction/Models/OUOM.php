<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OUOM extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_u_o_m_s';
}
