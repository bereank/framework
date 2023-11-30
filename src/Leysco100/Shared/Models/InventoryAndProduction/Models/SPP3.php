<?php


namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;

class SPP3 extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];
    public function uom()
    {
        return $this->belongsTo(OUOM::class, 'UomEntry');
    }

    public function spp2()
    {
        return $this->belongsTo(SPP2::class, 'SPP2Num');
    }
    public function item()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }
}
