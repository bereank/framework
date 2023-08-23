<?php

namespace Leysco100\Shared\Models\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Shared\Models\PDI1;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class APDI extends Model
{

    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'a_p_d_i_s';

    protected $appends = array('approval');
    public function pdi1()
    {
        return $this->hasMany(PDI1::class, 'DocEntry');
    }

    public function getApprovalAttribute()
    {
        if ($this->hasExtApproval == 0) {
            return "Enable";
        }

        if ($this->hasExtApproval == 1) {
            return "Disable";
        }
    }
}
