<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class WDD1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'w_d_d1_s';
}
