<?php

namespace Leysco100\Shared\Models\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OAT1 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_a_t1_s';

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function ooat()
    {
        return $this->belongsTo(OOAT::class, ' AgrNo');
    }
}
