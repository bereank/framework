<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class DPO1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_d_p_o_s';
}
