<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OSCL extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_s_c_l_s';

    public function solutions()
    {
        return $this->hasMany(SCL1::class, 'srvcCallID');
    }

    public function scl2()
    {
        return $this->hasMany(SCL2::class, 'SrcvCallID');
    }

    public function scl4()
    {
        return $this->hasMany(SCL4::class, 'SrcvCallID');
    }
}
