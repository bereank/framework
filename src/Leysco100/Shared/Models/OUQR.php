<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OUQR extends Model
{
    use HasFactory,UsesTenantConnection;
    protected $table= 'o_u_q_r';
    protected $guarded = [];
}
