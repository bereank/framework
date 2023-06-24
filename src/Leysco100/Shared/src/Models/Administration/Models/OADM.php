<?php

namespace App\Domains\Administration\Models;

use App\Domains\Marketing\Models\OPLN;
use Illuminate\Database\Eloquent\Model;

class OADM extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_a_d_m_s';

    public function opln()
    {
        return $this->belongsTo(OPLN::class, 'CostPrcLst');
    }
}
