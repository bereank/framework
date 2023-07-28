<?php

namespace Leysco100\Shared\Models;


use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Finance\Models\ChartOfAccount;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ITB1 extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'i_t_b1_s';

    public function glaccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'AcctCode');
    }
}
