<?php

namespace Leysco\LS100SharedPackage\Models\Domains\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OUOM;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\UGP1;

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
