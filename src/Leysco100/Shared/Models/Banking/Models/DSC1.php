<?php

namespace Leysco100\Shared\Models\Banking\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\Country;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class DSC1 extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'd_s_c1_s';

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
