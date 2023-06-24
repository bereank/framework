<?php

namespace App\Domains\Marketing\Models;

use App\Domains\InventoryAndProduction\Models\OITM;
use Illuminate\Database\Eloquent\Model;

class DPI1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'd_p_i1_s';
    public function odpi()
    {
        return $this->belongsTo('App\ODPI', 'DocEntry');
    }
    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }
}
