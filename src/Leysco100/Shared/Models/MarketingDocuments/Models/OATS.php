<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OATS extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_a_t_s';
}
