<?php

namespace Leysco100\Shared\Models\Banking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class RCT3 extends Model
{
    use UsesTenantConnection;

    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'r_c_t3_s';
}
