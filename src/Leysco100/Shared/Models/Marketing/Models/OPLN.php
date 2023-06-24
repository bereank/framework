<?php

namespace App\Domains\Marketing\Models;

use App\Domains\Administration\Models\Currency;
use App\Domains\InventoryAndProduction\Models\ITM1;
use Illuminate\Database\Eloquent\Model;

class OPLN extends Model
{
    //PRICE LIST
    protected $guarded = ['id'];
    protected $table = 'o_p_l_n_s';

    public function basenum()
    {
        return $this->belongsTo(OPLN::class, 'BASE_NUM');
    }

    public function PrimCurr()
    {
        return $this->belongsTo(Currency::class, 'PrimCurr');
    }

    public function addcurr1()
    {
        return $this->belongsTo(Currency::class, 'AddCurr1');
    }

    public function addcurr2()
    {
        return $this->belongsTo(Currency::class, 'AddCurr2');
    }

    public function itm1()
    {
        return $this->hasMany(ITM1::class, 'PriceList');
    }
}
