<?php

namespace Leysco100\Shared\Models\SalesOportunities\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Finance\Models\OPRC;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OCR1 extends Model
{
    //
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_c_r1_s';

    public function oprc()
    {
        return $this->belongsTo(OPRC::class, 'PrcCode','PrcCode');
    }
}

