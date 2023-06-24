<?php

namespace App\Models;

use App\Domains\Finance\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Model;

class ITB1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'i_t_b1_s';

    public function glaccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'AcctCode');
    }
}
