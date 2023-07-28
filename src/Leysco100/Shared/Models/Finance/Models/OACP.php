<?php

namespace Leysco100\Shared\Models\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Finance\Models\ACP10;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OACP extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_a_c_p_s';

    public function acp10()
    {
        return $this->hasMany(ACP10::class, 'PrdCtgyCode');
    }
}
