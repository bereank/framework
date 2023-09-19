<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\User;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ALT1 extends Model
{
    use UsesTenantConnection;
    
    protected $guarded = ['id'];
    protected $table = 'a_l_t1';
 
    public function users()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }
}