<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\WST1;

class OWST extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_w_s_t_s';
    public function wst1()
    {
        return $this->hasMany(WST1::class, 'WstCode');
    }
}
