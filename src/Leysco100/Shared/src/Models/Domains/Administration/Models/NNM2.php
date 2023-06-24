<?php

namespace Leysco\LS100SharedPackage\Models\Domains\Administration\Models;

use Illuminate\Database\Eloquent\Model;

class NNM2 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'n_n_m2_s';

    public function nnm1()
    {
        return $this->belongsTo(NNM1::class, 'Series');
    }
}
