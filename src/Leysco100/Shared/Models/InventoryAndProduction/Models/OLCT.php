<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OLCT extends Model
{

    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_l_c_t_s';
}
