<?php

namespace Leysco100\Shared\Models\Gpm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class BackUpModGates extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(config('auth.providers.users.model'), 'gate_id', 'GateID');
    }
}
