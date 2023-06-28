<?php

namespace Leysco100\Shared\Models\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class PDI1 extends Model
{

    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'p_d_i1_s';
}
