<?php

namespace App\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;

class OQUT extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_q_u_t_s';

    public function outlet()
    {
        return $this->belongsTo('App\OCRD', 'CardCode');
    }




    public function rows()
    {
        return $this->hasMany('App\QUT1', 'DocEntry');
    }


    public function objecttype()
    {
        return $this->belongsTo('App\APDI', 'ObjType', 'ObjectID');
    }
}
