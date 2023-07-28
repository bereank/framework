<?php

namespace Leysco100\Shared\Models\Administration\Models;


use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Country extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'countries';

    public function bank()
    {
        return $this->hasMany(Bank::class, 'country_id');
    }
}
