<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class POR1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'p_o_r1_s';
}
