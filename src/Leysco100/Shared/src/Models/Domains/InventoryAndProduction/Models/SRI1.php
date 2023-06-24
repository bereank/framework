<?php

namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models;

use App\Domains\InventoryAndProduction\Models\OSRN;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SRI1 extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 's_r_i1_s';

    public function osrn()
    {
        return $this->belongsTo(OSRN::class, 'SysSerial', 'SysNumber');
    }
}
