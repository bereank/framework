<?php

namespace App\Domains\Marketing\Models;

use App\Domains\Marketing\Models\OINV;
use Illuminate\Database\Eloquent\Model;
use App\Domains\Administration\Models\TaxGroup;
use App\Domains\InventoryAndProduction\Models\OITM;
use App\Domains\InventoryAndProduction\Models\OUOM;
use App\Domains\InventoryAndProduction\Models\SRI1;

class INV1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'i_n_v1_s';

    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function sri1()
    {
        return $this->hasMany(SRI1::class, 'LineNum', 'id');
    }
    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
    public function unitofmeasure()
    {
        return $this->belongsTo(OUOM::class, 'UomCode');
    }


    public function ordr()
    {
        return $this->belongsTo(OINV::class, 'DocEntry');
    }
}
