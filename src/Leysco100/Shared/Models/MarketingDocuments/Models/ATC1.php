<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ATC1 extends Model
{
    use UsesTenantConnection;

    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'a_t_c1_s';
}
