<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ORPD extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_r_p_d_s';
}
