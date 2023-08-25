<?php

namespace Leysco100\Shared\Models\Administration\Models;


use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OWTM extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_w_t_m_s';

    //Originators
    public function wtm1()
    {
        return $this->hasMany(WTM1::class, 'WtmCode');
    }
    //Stages
    public function wtm2()
    {
        return $this->hasMany(WTM2::class, 'WtmCode');
    }

    //Documents
    public function wtm3()
    {
        return $this->hasMany(WTM3::class, 'WtmCode');
    }
}
