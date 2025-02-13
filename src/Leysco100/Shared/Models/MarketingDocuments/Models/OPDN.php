<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OPDN extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_p_d_n_s';
}
