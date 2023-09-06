<?php

namespace Leysco100\Shared\Models\Banking\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\MarketingDocuments\Models\ODRF;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class PDF2 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'p_d_f2_s';

    public function invoice()
    {
        return $this->belongsTo(ODRF::class, 'DocEntry');
    }

    public function opdf()
    {
        return $this->belongsTo(OPDF::class, 'DocNum');
    }
}
