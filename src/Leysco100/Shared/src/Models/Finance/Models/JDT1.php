<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class JDT1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'j_d_t1_s';

    public function oact()
    {
        return $this->belongsTo(ChartOfAccount::class, 'Account');
    }
}
