<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class OJDT extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_j_d_t_s';

    public function jdt1()
    {
        return $this->hasMany(JDT1::class, 'TransId');
    }
}
