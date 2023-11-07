<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OBSL extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_b_s_l';

}
