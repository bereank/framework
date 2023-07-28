<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Leysco100\Shared\Models\ITB1;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OITB extends Model
{
    use UsesTenantConnection;
    //ITEM GROUPS
    protected $guarded = ['id'];
    protected $table = 'o_i_t_b_s';

    public function itb1()
    {
        return $this->hasMany(ITB1::class, 'ItmsGrpCod');
    }
}
