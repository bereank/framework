<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;


use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\ITG1;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OITG extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_i_t_g_s';

    public function itg1()
    {
        return $this->hasMany(ITG1::class, 'ItmsTypCod');
    }
}
