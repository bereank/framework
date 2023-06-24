<?php

namespace App\Domains\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;

class OIGE extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_i_g_e_s';

    public function rows()
    {
        return $this->hasMany('App\IGE1', 'DocEntry');
    }

    public function objecttype()
    {
        return $this->belongsTo('App\APDI', 'ObjType', 'ObjectID');
    }
}
