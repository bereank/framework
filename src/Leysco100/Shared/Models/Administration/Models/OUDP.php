<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OUDP extends Model
{
    use HasFactory,UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_u_d_p_s';
}
