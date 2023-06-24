<?php

namespace App\Domains\Banking\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Administration\Models\Country;

class DSC1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'd_s_c1_s';

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
