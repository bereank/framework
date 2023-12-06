<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Tier extends Model
{
    protected $guarded = ['id'];
    protected $table = 'tiers';

    use UsesTenantConnection;

    public function outlets()
    {
        return $this->hasMany(OCRD::class, 'TierCode');
    }

    public function employees()
    {
        return $this->hasMany(OSLP::class, 'TierCode');
    }
}
