<?php

namespace Leysco100\Shared\Models\Administration\Models;


use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\Administration\Models\NNM1;
use Leysco100\Shared\Models\Administration\Models\NNM2;

class ONNM extends Model
{
    //numbering series
    protected $guarded = ['id'];
    protected $table = 'o_n_n_m_s';

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjectCode');
    }

    public function defaultseries()
    {
        return $this->belongsTo(NNM1::class, 'DfltSeries');
    }

    public function series()
    {
        return $this->hasMany(NNM1::class, 'ObjectCode');
    }

    public function nnm2()
    {
        return $this->hasMany(NNM2::class, 'ObjectCode');
    }
}
