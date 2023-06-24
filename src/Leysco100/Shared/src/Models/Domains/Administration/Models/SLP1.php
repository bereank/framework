<?php

namespace Leysco\LS100SharedPackage\Models\Domains\Administration\Models;

use Illuminate\Database\Eloquent\Model;

class SLP1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 's_l_p1_s';

    public function regions()
    {
        return $this->belongsTo(OTER::class, 'Territory');
    }
}
