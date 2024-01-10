<?php

namespace Leysco100\Shared\Models\LogisticsHub\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OGPS extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];

    public function workDays()
    {
        return $this->hasMany(WorkdaySetup::class, 'gps_setup_id', 'id');
    }
}
