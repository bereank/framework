<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class SCL2 extends Model
{
    use UsesTenantConnection;

    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 's_c_l2_s';
}
