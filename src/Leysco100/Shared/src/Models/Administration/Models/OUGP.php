<?php

namespace App\Domains\Administration\Models;

use App\Domains\InventoryAndProduction\Models\OUOM;
use App\Domains\InventoryAndProduction\Models\UGP1;
use Illuminate\Database\Eloquent\Model;

class OUGP extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_u_g_p_s';

    public function ouom()
    {
        return $this->belongsTo(OUOM::class, 'BaseUom');
    }

    public function ugp1()
    {
        return $this->hasMany(UGP1::class, 'UgpEntry');
    }
}
