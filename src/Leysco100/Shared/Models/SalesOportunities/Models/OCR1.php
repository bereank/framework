<?php

namespace Leysco100\Shared\Models\SalesOportunities\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OCR1 extends Model
{
    //
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_c_r1_s';
}
