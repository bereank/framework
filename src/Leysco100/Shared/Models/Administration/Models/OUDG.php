<?php

namespace Leysco100\Shared\Models\Administration\Models;


use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\BusinessPartner\Models\OBPL;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OWHS;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OUDG extends Model
{

    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_u_d_g_s';

    public function employee()
    {
        return $this->belongsTo(OSLP::class, 'SalePerson');
    }

    public function driver()
    {
        return $this->belongsTo(ORLP::class, 'Driver');
    }

    public function warehouse()
    {
        return $this->belongsTo(OWHS::class, 'Warehouse');
    }

    public function branch()
    {
        return $this->belongsTo(OBPL::class, 'BPLId', 'BPLId');
    }
}
