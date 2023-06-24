<?php

namespace App\Domains\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;

class IGN1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'i_g_n1_s';

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }
}
