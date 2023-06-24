<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class OPRC extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_p_r_c_s';

    public function odim()
    {
        return $this->belongsTo(ODIM::class, 'DimCode');
    }

    public function BusinessPartner()
    {
        return $this->belongsTo(OCRD::class, 'CardCode', 'CardCode');
    }

    public function costcentertype()
    {
        return $this->belongsTo(OCCT::class, 'CCTypeCode');
    }
}
