<?php

namespace Leysco100\Shared\Models;

use Leysco100\Shared\Models\Administration\Models\ORLP;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;
use Leysco100\Shared\Models\MarketingDocuments\Models\ORDR;

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
