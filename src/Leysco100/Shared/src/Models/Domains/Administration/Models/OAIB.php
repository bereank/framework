<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Administration\Models;

use Illuminate\Database\Eloquent\Model;

class OAIB extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_a_i_b_s';

    public function oalr()
    {
        return $this->belongsTo(OALR::class, 'AlertCode');
    }
}
