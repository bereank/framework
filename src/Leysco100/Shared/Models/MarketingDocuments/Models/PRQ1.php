<?php

namespace App\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Administration\Models\TaxGroup;

class PRQ1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'p_r_q1_s';

    public function sri1()
    {
        return $this->belongsTo(SRI1::class, 'ItemCode', 'ItemCode');
    }

    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
}
