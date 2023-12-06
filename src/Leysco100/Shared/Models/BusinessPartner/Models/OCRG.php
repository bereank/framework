<?php

namespace Leysco100\Shared\Models\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPLN;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OCRG extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_c_r_g_s';

    public function opln()
    {
        return $this->belongsTo(OPLN::class, 'PriceList');
    }
}
