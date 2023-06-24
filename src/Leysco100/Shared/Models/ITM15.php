<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ITM15 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'i_t_m15_s';

    public function itg1()
    {
        return $this->belongsTo('App\ITG1', 'QryGroup');
    }

    public function oitg()
    {
        return $this->belongsTo('App\OITG', 'ItmsTypCod');
    }
}
