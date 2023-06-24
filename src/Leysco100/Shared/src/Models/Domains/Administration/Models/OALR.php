<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Administration\Models;

use Illuminate\Database\Eloquent\Model;

class OALR extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_a_l_r_s';

    public function odrf()
    {
        return $this->belongsTo(ODRF::class, 'DraftEntry');
    }

    public function sendby()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }
}
