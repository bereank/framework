<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Finance\Models\ChartOfAccount;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class WHS1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'w_h_s1_s';

    public function glaccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'AcctCode');
    }
}
