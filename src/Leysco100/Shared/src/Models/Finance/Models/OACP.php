<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class OACP extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_a_c_p_s';

    public function acp10()
    {
        return $this->hasMany(ACP10::class, 'PrdCtgyCode');
    }
}
