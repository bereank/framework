<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class NNM1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'n_n_m1_s';
}
