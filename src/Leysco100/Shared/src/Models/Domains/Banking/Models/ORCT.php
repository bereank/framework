<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Banking\Models;

use App\Domains\Banking\Models\RCT1;
use App\Domains\Banking\Models\RCT2;
use Illuminate\Database\Eloquent\Model;

class ORCT extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_r_c_t_s';

    public function rct2()
    {
        return $this->hasMany(RCT2::class, 'DocNum');
    }

    public function rct1()
    {
        return $this->hasMany(RCT1::class, 'DocNum');
    }
}
