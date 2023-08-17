<?php

namespace App\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;

class ODPI extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_d_p_i_s';

    public function items()
    {
        return $this->hasMany('App\DPI1', 'DocEntry');
    }
    public function outlet()
    {
        return $this->belongsTo('App\OCRD', 'DocEntry');
    }

    public function rows()
    {
        return $this->hasMany('App\DPI1', 'DocEntry');
    }

    public function objecttype()
    {
        return $this->belongsTo('App\APDI', 'ObjType', 'ObjectID');
    }
}
