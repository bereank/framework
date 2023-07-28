<?php

namespace Leysco100\Shared\Models\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Finance\Models\ChartOfAccount;

class ACP10 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'a_c_p10_s';

    public function glaccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'AcctCode');
    }
}
