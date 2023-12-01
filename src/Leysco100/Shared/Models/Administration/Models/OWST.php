<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OWST extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_w_s_t_s';
    public function wst1()
    {
        return $this->hasMany(WST1::class, 'WstCode');
    }
}
