<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OQCN extends Model
{
    use HasFactory,UsesTenantConnection;
    protected $table= 'o_q_c_n';
    protected $guarded = [];
}
