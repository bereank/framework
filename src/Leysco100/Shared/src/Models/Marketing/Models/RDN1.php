<?php

namespace App\Domains\Marketing\Models;

use App\Domains\Administration\Models\TaxGroup;
use App\Domains\InventoryAndProduction\Models\OITM;
use App\Domains\InventoryAndProduction\Models\OUOM;
use App\Domains\InventoryAndProduction\Models\SRI1;
use Illuminate\Database\Eloquent\Model;

class RDN1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'r_d_n1_s';

    public function ordn()
    {
        return $this->belongsTo(ORDN::class, 'DocEntry');
    }

    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function unitofmeasure()
    {
        return $this->belongsTo(OUOM::class, 'UomCode');
    }

    public function sri1()
    {
        return $this->hasMany(SRI1::class, 'LineNum', 'id');
    }

    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
}
