<?php

namespace Leysco100\Shared\Models\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class JDT1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'j_d_t1_s';

    public function oact()
    {
        return $this->belongsTo(ChartOfAccount::class, 'Account');
    }
}
