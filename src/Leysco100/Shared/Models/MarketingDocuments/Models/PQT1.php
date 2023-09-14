<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class PQT1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'p_q_r1_s';
}
