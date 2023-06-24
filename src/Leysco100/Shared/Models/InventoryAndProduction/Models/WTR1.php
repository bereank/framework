<?php

namespace App\Domains\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OITM;

class WTR1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'w_t_r1_s';

    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function sri1()
    {
        return $this->hasMany(SRI1::class, 'ItemCode', 'ItemCode');
    }
}
