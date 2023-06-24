<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\WTM1;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\WTM2;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\WTM3;

class OWTM extends Model
{
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
