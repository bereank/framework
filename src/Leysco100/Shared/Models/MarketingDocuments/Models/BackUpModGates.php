<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco\GatePassManagementModule\Models\BackUpModUsers;
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
