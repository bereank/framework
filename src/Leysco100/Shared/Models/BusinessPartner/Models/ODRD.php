<?php

namespace Leysco100\Shared\Models\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ODRD extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_d_r_d_s';
}
