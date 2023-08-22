<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OWDD extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_w_d_d_s';
}
