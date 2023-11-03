<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Leysco100\Shared\Models\OUQR;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ALT6 extends Model
{
    use UsesTenantConnection;
    
    protected $guarded = ['id'];
    protected $table = 'a_l_t6';
 
    public function saved_query()
    {
        return $this->belongsTo(OUQR::class, 'QueryId');
    }
}