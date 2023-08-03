<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OITW extends Model
{

    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_i_t_w_s';



    public function ITM1()
    {
        return $this->hasMany('App\ITM1');
    }
}
