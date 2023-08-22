<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;

class OSRN extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_s_r_n_s';

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }
}
