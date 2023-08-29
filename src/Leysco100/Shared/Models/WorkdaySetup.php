<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class WorkdaySetup extends Model
{
    use HasFactory, UsesTenantConnection;

    public function gps()
    {
        return $this->belongsTo(GpsSetup::class, 'gps_setup_id');
    }
}
