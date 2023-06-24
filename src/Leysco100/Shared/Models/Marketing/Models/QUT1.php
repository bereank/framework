<?php

namespace App\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;

class QUT1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'q_u_t1_s';

    public function oitm()
    {
        return $this->belongsTo('App\OITM', 'ItemCode');
    }
}
