<?php

namespace App\Domains\Administration\Models;

use App\Domains\Administration\Models\ORLP;
use App\Domains\Administration\Models\OSLP;
use App\Domains\BusinessPartner\Models\OBPL;
use App\Domains\InventoryAndProduction\Models\OWHS;
use Illuminate\Database\Eloquent\Model;

class OUDG extends Model
{
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
