<?php

namespace App\Domains\Administration\Models;

use App\Domains\Shared\Models\APDI;
use Illuminate\Database\Eloquent\Model;

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
