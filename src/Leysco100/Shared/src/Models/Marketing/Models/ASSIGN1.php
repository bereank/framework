<?php

namespace App\Domains\Marketing\Models;

use App\Domains\Marketing\Models\OINV;
use Illuminate\Database\Eloquent\Model;
use App\Domains\Marketing\Models\OASSIGN;
use App\Domains\InventoryAndProduction\Models\OITM;
use App\Domains\InventoryAndProduction\Models\OUOM;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ASSIGN1 extends Model
{
    use HasFactory;



    protected $guarded = ['id'];
    protected $table = 'a_s_s_i_g_n1_s';


    public function unitofmeasure()
    {
        return $this->belongsTo(OUOM::class, 'UomCode');
    }

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }


    public function ordr()
    {
        return $this->belongsTo(OASSIGN::class, 'DocEntry');
    }


    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }
}
