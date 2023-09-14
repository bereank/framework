<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class PDN1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'p_d_n1_s';
}
