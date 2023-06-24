<?php

namespace App\Domains\InventoryAndProduction\Models;

use App\Domains\Administration\Models\OUGP;
use Illuminate\Database\Eloquent\Model;

class UGP1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'u_g_p1_s';

    public function uomentry()
    {
        return $this->belongsTo(OUOM::class, 'UomEntry');
    }

    public function baseuom()
    {
        return $this->belongsTo(OUGP::class, 'UgpEntry');
    }

    public function uom()
    {
        return $this->belongsTo(OUOM::class, 'UomEntry');
    }
}
