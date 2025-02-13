<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class TaxGroup extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'tax_groups';
}
