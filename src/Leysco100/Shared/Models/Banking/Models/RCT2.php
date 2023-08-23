<?php

namespace Leysco100\Shared\Models\Banking\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\MarketingDocuments\Models\OINV;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class RCT2 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'r_c_t2_s';

    public function invoice()
    {
        return $this->belongsTo(OINV::class, 'DocEntry');
    }

    public function orct()
    {
        return $this->belongsTo(ORCT::class, 'DocNum');
    }
}
