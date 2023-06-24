<?php
namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\OUGP;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OUOM;

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
