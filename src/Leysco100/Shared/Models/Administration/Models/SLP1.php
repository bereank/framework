<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class SLP1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 's_l_p1_s';

    public function regions()
    {
        return $this->belongsTo(OTER::class, 'Territory');
    }
}
