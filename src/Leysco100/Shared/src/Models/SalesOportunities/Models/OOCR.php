<?php

namespace App\Domains\SalesOportunities\Models;

use App\Domains\Finance\Models\ODIM;
use Illuminate\Database\Eloquent\Model;

class OOCR extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_o_c_r_s';

    public function odim()
    {
        return $this->belongsTo(ODIM::class, 'DimCode');
    }
}
