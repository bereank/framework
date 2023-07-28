<?php

namespace Leysco100\Shared\Models\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;

class CRD15 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'c_r_d15_s';

    public function cqg1()
    {
        return $this->belongsTo(CQG1::class, 'QryGroup');
    }

    public function ocqg()
    {
        return $this->belongsTo(OCQG::class, 'GroupCode');
    }
}
