<?php

namespace Leysco100\Shared\Models\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OJDT extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_j_d_t_s';

    public function jdt1()
    {
        return $this->hasMany(JDT1::class, 'TransId');
    }
}
