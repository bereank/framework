<?php

namespace Leysco100\Shared\Models\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OCRP extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_c_r_p_s';
}
