<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class GpsSetup extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];

    public function workDays()
    {
        return $this->hasMany(WorkdaySetup::class, 'gps_setup_id', 'id');
    }
}
