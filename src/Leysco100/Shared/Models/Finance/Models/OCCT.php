<?php

namespace Leysco100\Shared\Models\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OCCT extends Model
{
    //
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_c_c_t_s';
}
