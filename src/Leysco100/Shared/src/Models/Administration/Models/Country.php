<?php

namespace App\Domains\Administration\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $guarded = ['id'];
    protected $table = 'countries';

    public function bank()
    {
        return $this->hasMany(Bank::class, 'country_id');
    }
}
