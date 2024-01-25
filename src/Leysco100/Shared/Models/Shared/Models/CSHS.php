<?php

namespace Leysco100\Shared\Models\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class CSHS extends Model
{
    use UsesTenantConnection, HasFactory;
    protected $guarded = ['id'];
}
