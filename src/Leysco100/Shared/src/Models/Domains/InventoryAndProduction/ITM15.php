<?php

namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction;

use Illuminate\Database\Eloquent\Model;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\ITG1;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OITG;

class ITM15 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'i_t_m15_s';

    public function itg1()
    {
        return $this->belongsTo(ITG1::class, 'QryGroup');
    }

    public function oitg()
    {
        return $this->belongsTo(OITG::class, 'ItmsTypCod');
    }
}
