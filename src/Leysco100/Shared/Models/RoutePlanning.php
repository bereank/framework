<?php

namespace App\Models;

use App\Domains\Administration\Models\User;
use App\Domains\BusinessPartner\Models\OCLG;
use App\Domains\BusinessPartner\Models\OCRD;
use Illuminate\Database\Eloquent\Model;

class RoutePlanning extends Model
{
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
}
