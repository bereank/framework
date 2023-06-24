<?php

namespace App\Domains\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;

class OOAT extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_o_a_t_s';

    public function oat1()
    {
        return $this->hasMany('App\OAT1', 'AgrNo');
    }

    public function oat4()
    {
        return $this->hasMany('App\OAT4', 'AgrNo');
    }

    public function Items()
    {
        return $this->hasMany('App\OAT1', 'AgrNo');
    }

    public function buyer()
    {
        return $this->belongsTo('App\OCRD', 'BpCode');
    }
}
