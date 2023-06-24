<?php

namespace Leysco\LS100SharedPackage\Models\Domains\Finance\Models;

use App\Domains\Finance\Models\ACP10;
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
