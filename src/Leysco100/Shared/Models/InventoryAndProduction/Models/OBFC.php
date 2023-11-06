<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OBFC extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_b_f_c';

}
