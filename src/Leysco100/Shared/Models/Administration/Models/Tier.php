<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\OSLP;

class Tier extends Model
{
    protected $guarded = ['id'];
    protected $table = 'tiers';

    public function outlets()
    {
        return $this->hasMany(OCRD::class, 'TierCode');
    }

    public function employees()
    {
        return $this->hasMany(OSLP::class, 'TierCode');
    }
}
