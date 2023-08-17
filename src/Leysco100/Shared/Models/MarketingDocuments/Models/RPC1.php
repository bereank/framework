<?php

namespace App\Domains\Marketing\Models;

use App\Domains\InventoryAndProduction\Models\OITM;
use App\Domains\InventoryAndProduction\Models\OUOM;
use App\Domains\InventoryAndProduction\Models\SRI1;
use Illuminate\Database\Eloquent\Model;

class RPC1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'r_p_c1_s';

    public function orpc()
    {
        return $this->belongsTo(ORPC::class, 'DocEntry');
    }

    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function unitofmeasure()
    {
        return $this->belongsTo(OUOM::class, 'UomCode');
    }

    public function sri1()
    {
        return $this->belongsTo(SRI1::class, 'ItemCode', 'ItemCode');
    }
}
