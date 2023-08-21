<?php

namespace Leysco100\Shared\Models\SalesOportunities\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Shared\Models\ODIM;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OOCR extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_o_c_r_s';

    public function odim()
    {
        return $this->belongsTo(ODIM::class, 'DimCode');
    }
}
