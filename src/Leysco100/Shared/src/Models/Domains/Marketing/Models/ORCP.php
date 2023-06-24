<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;

class ORCP extends Model
{
    //o_r_c_p_s
    protected $guarded = ['id'];
    protected $table = 'o_r_c_p_s';

    public function odrf()
    {
        return $this->belongsTo('App\ODRF', 'DraftEntry');
    }

    public function orcl()
    {
        return $this->hasOne('App\ORCL', 'RcpEntry');
    }
}
