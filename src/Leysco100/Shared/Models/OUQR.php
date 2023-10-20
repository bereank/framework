<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\Administration\Models\User;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OUQR extends Model
{
    use HasFactory,UsesTenantConnection;
    protected $table= 'o_u_q_r';
    protected $guarded = [];

    
    public function creator()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }
}
