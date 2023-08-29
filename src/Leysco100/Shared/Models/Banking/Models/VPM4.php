<?php

namespace Leysco100\Shared\Models\Banking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class VPM4 extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'v_p_m4_s';
}
