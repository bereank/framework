<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class WST1 extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'w_s_t1_s';

    public function users()
    {
        return $this->belongsTo(User::class, 'UserID');
    }
}
