<?php

namespace Leysco100\Shared\Models\Banking\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\Country;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Bank extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'banks';

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
