<?php

namespace App\Models;

use App\Domains\Marketing\Models\ORDR;
use Illuminate\Database\Eloquent\Model;
use App\Domains\Administration\Models\ORLP;
use App\Domains\InventoryAndProduction\Models\OITM;
use App\Domains\InventoryAndProduction\Models\OUOM;

class ODIS extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_d_i_s';

    public function ordr()
    {
        return $this->belongsTo(ORDR::class, 'DocEntry');
    }
    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function orlp()
    {
        return $this->belongsTo(ORLP::class, 'AssEmp');
    }

    public function document()
    {
        return $this->belongsTo(ORDR::class, 'DocEntry');
    }
    public function unitofmeasure()
    {
        return $this->belongsTo(OUOM::class, 'UomCode');
    }
}
