<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ITG1 extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'i_t_g1_s';
}
