<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class GpsLocation extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];

    use HasFactory;
}
