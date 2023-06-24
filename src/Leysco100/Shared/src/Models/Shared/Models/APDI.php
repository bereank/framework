<?php

namespace Leysco100\Shared\Models\Shared\Models;

use Illuminate\Database\Eloquent\Model;

class APDI extends Model
{
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
