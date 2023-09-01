<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;


class MobileErrorLog extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = [];

    public function user()
    {
        return $this->hasOne(config('auth.providers.users.model'), 'id', 'UserSign');
    }
}