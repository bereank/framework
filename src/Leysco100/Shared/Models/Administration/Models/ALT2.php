<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\Role;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ALT2 extends Model
{
    use UsesTenantConnection;
    
    protected $guarded = ['id'];
    protected $table = 'a_l_t2';
 
    public function group()
    {
        return $this->belongsTo(Role::class, 'GroupId');
    }
}