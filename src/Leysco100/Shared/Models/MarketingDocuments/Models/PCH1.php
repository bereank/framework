<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class PCH1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'p_c_h1_s';
}
