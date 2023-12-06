<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\OTER;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OCLG;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class RoutePlanning extends Model
{
    Use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'route_plannings';

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rows()
    {
        return $this->hasMany(RouteOutlet::class, 'route_id');
    }

    public function bpartners()
    {
        return $this->belongsToMany(OCRD::class, RouteOutlet::class, 'route_id', 'outlet_id');
    }

    public function calls()
    {
        return $this->hasMany(OCLG::class, 'RouteCode');
    }

    public function territory()
    {
        return $this->belongsTo(OTER::class, 'territory_id');
    }
}
