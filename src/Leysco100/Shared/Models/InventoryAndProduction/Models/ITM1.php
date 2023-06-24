<?php

namespace App\Domains\InventoryAndProduction\Models;

use App\Domains\Marketing\Models\OPLN;
use Illuminate\Database\Eloquent\Model;

class ITM1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'i_t_m1_s';

    protected $appends = array('currency');
    public function OITW()
    {
        return $this->belongsTo(OITW::class);
    }

    public function item()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function opln()
    {
        return $this->belongsTo(OPLN::class, 'PriceList');
    }

    public function getCurrencyAttribute()
    {
        return "KES";
    }
}
