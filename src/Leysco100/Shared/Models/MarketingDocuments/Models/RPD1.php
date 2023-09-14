<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class RPD1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'r_p_d1_s';
}
