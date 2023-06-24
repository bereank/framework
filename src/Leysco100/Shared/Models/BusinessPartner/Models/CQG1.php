<?php

namespace App\Domains\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;

class CQG1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'c_q_g1_s';

    public function ocqg()
    {
        return $this->belongsTo(OCQG::class, 'GroupCode');
    }
}
