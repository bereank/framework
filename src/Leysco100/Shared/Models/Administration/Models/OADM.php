<?php

namespace Leysco100\Shared\Models\Administration\Models;

use App\Domains\Marketing\Models\OPLN;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OADM extends Model
{

    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_a_d_m_s';

    public function opln()
    {
        return $this->belongsTo(OPLN::class, 'CostPrcLst');
    }
}
