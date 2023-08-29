<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class RouteOutlet extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'route_outlets';
}
