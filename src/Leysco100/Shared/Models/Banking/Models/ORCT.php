<?php

namespace App\Domains\Banking\Models;

use Illuminate\Database\Eloquent\Model;

class ORCT extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_r_c_t_s';

    public function rct2()
    {
        return $this->hasMany(RCT2::class, 'DocNum');
    }

    public function rct1()
    {
        return $this->hasMany(RCT1::class, 'DocNum');
    }
}
