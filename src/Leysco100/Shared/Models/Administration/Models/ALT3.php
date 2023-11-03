<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ALT3 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'a_l_t3';
}
